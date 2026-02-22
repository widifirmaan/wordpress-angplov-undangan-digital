<?php

namespace YahnisElsts\AdminMenuEditor\ContentPermissions\UserInterface;

use YahnisElsts\AdminMenuEditor\Actors\ActorManager;
use YahnisElsts\AdminMenuEditor\Actors\User;
use YahnisElsts\AdminMenuEditor\ContentPermissions\ContentPermissionsEnforcer;
use YahnisElsts\AdminMenuEditor\ContentPermissions\ContentPermissionsModule;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\Action;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\ActionRegistry;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\ContentItemPolicy;
use YahnisElsts\AdminMenuEditor\ContentPermissions\Policy\PolicyStore;
use YahnisElsts\AdminMenuEditor\Customizable\Storage\AbstractSettingsDictionary;

class ContentPermissionsMetaBox {
	const BOX_ID = 'ame-cpe-content-permissions';
	const BOX_NONCE_NAME = 'ame_cpe_box_nonce';
	const BOX_NONCE_ACTION = 'ame_cpe_box_save_policy';

	const POLICY_FIELD_NAME = 'ame_cpe_policy_data';
	const TERM_POST_TYPE_FIELD_NAME = 'ame_cpe_term_post_type';
	const TERM_UNIVERSAL_POLICY_FIELD_NAME = 'ame_cpe_is_universal_term_policy';

	/**
	 * @var PolicyStore
	 */
	private PolicyStore $policyStore;
	/**
	 * @var ContentPermissionsModule
	 */
	private ContentPermissionsModule $module;
	/**
	 * @var ActionRegistry
	 */
	private ActionRegistry $actionRegistry;
	/**
	 * @var ContentPermissionsEnforcer
	 */
	private ContentPermissionsEnforcer $enforcer;
	/**
	 * @var string Full URL to the main plugin settings page.
	 */
	private string $settingsPageUrl;
	/**
	 * @var ActorManager
	 */
	private ActorManager $actorManager;

	public function __construct(
		ActionRegistry             $actionRegistry,
		ActorManager               $actorManager,
		PolicyStore                $policyStore,
		ContentPermissionsModule   $module,
		ContentPermissionsEnforcer $enforcer,
		                           $settingsPageUrl
	) {
		$this->policyStore = $policyStore;
		$this->module = $module;
		$this->actionRegistry = $actionRegistry;
		$this->actorManager = $actorManager;
		$this->enforcer = $enforcer;
		$this->settingsPageUrl = $settingsPageUrl;

		foreach (['load-post.php', 'load-post-new.php'] as $hook) {
			add_action($hook, [$this, 'addBoxHooks']);
		}

		if ( $module->areTermPermissionsEnabled() ) {
			//Note: The "load-edit-tags.php" hook is only needed for saving policies.
			foreach (['load-term.php', 'load-edit-tags.php'] as $hook) {
				add_action($hook, [$this, 'addTermUiHooks']);
			}
		}
	}

	public function addBoxHooks() {
		if ( !is_textdomain_loaded('admin-menu-editor') ) {
			load_plugin_textdomain('admin-menu-editor');
		}

		add_action('add_meta_boxes', [$this, 'addMetaBox'], 8, 2);
		add_action('save_post', [$this, 'saveMetaBox'], 10, 2);

		$currentScreen = get_current_screen();
		if (
			$currentScreen
			&& ($currentScreen->base === 'post')
			&& $this->isSomePostTypeEnabled($currentScreen->post_type)
		) {
			add_action('admin_enqueue_scripts', [$this, 'enqueueMetaBoxDependencies']);
		}
	}

	public function addTermUiHooks() {
		if ( !is_textdomain_loaded('admin-menu-editor') ) {
			load_plugin_textdomain('admin-menu-editor');
		}

		$screen = get_current_screen();
		if ( !$screen || !$screen->taxonomy ) {
			return;
		}
		$taxonomy = get_taxonomy($screen->taxonomy);
		if ( !$taxonomy || empty($taxonomy->name) ) {
			return;
		}

		//Is this taxonomy associated with any of the enabled post types?
		$postTypes = $taxonomy->object_type;
		if ( empty($postTypes) || !$this->isSomePostTypeEnabled($postTypes) ) {
			return;
		}

		add_action($taxonomy->name . '_edit_form', [$this, 'renderTermBox']);
		add_action('admin_enqueue_scripts', [$this, 'enqueueMetaBoxDependencies']);

		/**
		 * The "Edit [Term]" form is submitted to edit-tags.php, which then calls wp_update_term().
		 *
		 * @see wp_update_term() where the "edited_{$taxonomy}" action is triggered.
		 */
		add_action('edited_' . $taxonomy->name, [$this, 'saveTermBox'], 10, 3);
	}

	/**
	 * @param string $postType
	 * @param \WP_Post $post
	 * @return void
	 * @noinspection PhpUnusedParameterInspection -- Signature required by the add_meta_boxes action.
	 * @noinspection PhpMissingParamTypeInspection -- Technically, should have well-defined types, but who knows what WP will pass here.
	 */
	public function addMetaBox($postType, $post = null) {
		if ( $post && !$this->module->userCanEditPolicyForPost($post->ID) ) {
			return;
		}

		add_meta_box(
			self::BOX_ID,
			__('Content Permissions (AME)', 'admin-menu-editor'),
			[$this, 'renderMetaBox'],
			array_values($this->getEnabledPostTypes()),
			'advanced',
			'high'
		);
	}

	public function renderMetaBox($post) {
		if ( !$this->module->userCanEditPolicyForPost($post->ID) ) {
			//This should never happen since the box won't be added if the user doesn't have
			//permission, but let's handle it just in case.
			echo '<p>' . esc_html__(
					'You do not have permission to edit the content permissions for this post.',
					'admin-menu-editor'
				) . '</p>';
			return;
		}

		$this->renderPolicyEditorUi(Action::OBJECT_TYPE_POST, $post);
	}

	private function renderPolicyEditorUi(
		string         $objectType,
		               $nativeObject,
		?\WP_Post_Type $postType = null,
		?\WP_Taxonomy  $taxonomy = null
	) {
		$isTermUi = ($objectType === Action::OBJECT_TYPE_TERM);

		//Nonce field(s). The referer field may be redundant if another plugin also adds a meta box
		//with its own nonce fields, but we can't easily detect that.
		wp_nonce_field(
			self::BOX_NONCE_ACTION,
			self::BOX_NONCE_NAME,
			//No referer for terms since the core term form already has one.
			!$isTermUi
		);

		//Fetch the actions that will be shown in the UI.
		$applicableActions = $this->actionRegistry->getApplicableActions($objectType, $nativeObject);
		//Remove the "publish" action since it's not fully implemented/supported.
		$applicableActions = array_filter($applicableActions, function (Action $action) {
			return $action->getName() !== 'publish';
		});

		//Some labels have dynamic parts that depend on the post type or taxonomy,
		//so we can't just rely on implicit action serialization.
		$serializedActions = array_map(function (Action $action) use ($postType, $taxonomy) {
			$data = $action->jsonSerialize();
			$data['label'] = $action->getLabel($postType, $taxonomy);
			return $data;
		}, $applicableActions);

		//Figure out what capabilities are required for each action. This is used to populate
		//the default permissions for each action (only for presentation purposes).
		$requiredCapabilities = $this->enforcer->runWithoutPolicyEnforcement(
			[$this, 'detectRequiredCapsFor'],
			$applicableActions,
			$nativeObject,
			$postType,
			$taxonomy
		);

		//For terms, we put actions into groups with explanatory labels so that actions themselves
		//can have shorter labels.
		$groupLabels = [];
		if ( !empty($taxonomy) ) {
			if ( !empty($postType) ) {
				$groupLabels[ActionRegistry::GROUP_ASSOCIATED_POSTS] = sprintf(
				/* translators: %1$s: post type name, %2$s: taxonomy singular name */
					_x('Permissions for %1$s assigned to this %2$s', 'content permissions: group heading', 'admin-menu-editor'),
					$postType->labels->name,
					$taxonomy->labels->singular_name
				);
			}

			$groupLabels[ActionRegistry::GROUP_THIS_TERM] = sprintf(
			/* translators: %s: taxonomy singular name */
				_x('Permissions for this %s', 'content permissions: group heading', 'admin-menu-editor'),
				$taxonomy->labels->singular_name
			);
		}

		//Load the policy for the item.
		$policy = $this->policyStore->getObjectPolicy($objectType, $nativeObject, $postType ? $postType->name : null);

		$moduleSettings = $this->module->loadSettings();

		/** @noinspection PhpArrayWriteIsNotUsedInspection -- Used in the template and passed to JS. */
		$editorData = [
			'applicableActions'    => $serializedActions,
			'groupLabels'          => $groupLabels,
			'requiredCapabilities' => $requiredCapabilities,
			'policy'               => $policy,
			'enforcementDisabled'  => boolval($moduleSettings['enforcementDisabled']),
			'adminLikeRoles'       => $this->getAdminLikeRoles(),
		];
		$cpeSettingsUrl = $this->settingsPageUrl . '#ame-content-permissions-section';
		$cpeModulesUrl = $this->settingsPageUrl . '#ame-available-modules';
		if ( $isTermUi ) {
			$cpeTermPostType = !empty($postType) ? $postType->name : '';
			$cpeTermIsUniversalPolicy = empty($postType);
		} else {
			$cpeTermPostType = null;
			$cpeTermIsUniversalPolicy = false;
		}

		if ( $isTermUi ) {
			require __DIR__ . '/term-box-template.php';
		} else {
			require __DIR__ . '/metabox-template.php';
		}
	}

	/**
	 * This method should be called through ContentPermissionsEnforcer::runWithoutPolicyEnforcement()
	 * for accurate results.
	 *
	 * @private It's public only so it can be used as a callback.
	 *
	 * @param Action[] $actions
	 * @param $nativeObject
	 * @param \WP_Post_Type|null $postType
	 * @param \WP_Taxonomy|null $taxonomy
	 * @return array
	 */
	public function detectRequiredCapsFor(
		array          $actions,
		               $nativeObject,
		?\WP_Post_Type $postType = null,
		?\WP_Taxonomy  $taxonomy = null
	): array {
		if ( empty($actions) ) {
			return [];
		}

		if ( $nativeObject instanceof \WP_Post ) {
			$postStatus = get_post_status_object($nativeObject->ID);
			$isDraftLike = !$postStatus || (!$postStatus->public && !$postStatus->private);
			if ( !$postType ) {
				$postType = get_post_type_object($nativeObject->post_type);
			}
		} else {
			$isDraftLike = false;
		}

		$requiredCapabilities = [];
		foreach ($actions as $action) {
			$postMetaCap = $action->getPostMetaCap();
			$termMetaCap = $action->getTermMetaCap();

			$objectId = null;
			$chosenMetaCap = null;

			if ( ($nativeObject instanceof \WP_Post) && !empty($postMetaCap) ) {
				//Special case: The trick we use to get required caps through map_meta_cap() doesn't
				//work so well for drafts because the logic in the "read_post" case ends up calling
				//map_meta_cap() recursively with "edit_post" and returns "edit_others_posts" or similar.
				//Instead, let's look at post type capabilities directly.
				if ( $isDraftLike && ($postMetaCap === 'read_post') ) {
					if ( $postType && !empty($postType->cap->read) ) {
						$requiredCapabilities[$action->getName()] = [$postType->cap->read];
					}
					continue;
				}

				$objectId = $nativeObject->ID;
				$chosenMetaCap = $postMetaCap;

			} else if ( ($nativeObject instanceof \WP_Term) ) {
				if ( !empty($termMetaCap) ) {
					$objectId = $nativeObject->term_id;
					$chosenMetaCap = $termMetaCap;
				} else if ( !empty($postMetaCap) ) {
					//We don't have a post object here, so no object ID. We can still get
					//the capabilities from the post type.
					$chosenMetaCap = $postMetaCap;
				}
			}

			if ( $chosenMetaCap ) {
				$caps = $this->tryToMapMetaCap($chosenMetaCap, $objectId, $postType, $taxonomy);
				if ( !empty($caps) ) {
					$requiredCapabilities[$action->getName()] = $caps;
				}
			}
		}

		return $requiredCapabilities;
	}

	private function tryToMapMetaCap($metaCap, $objectId, $postType, $taxonomy): array {
		if ( $objectId ) {
			/*
			By default, we detect capabilities by calling map_meta_cap() with user ID 0.
			Unfortunately, this causes some plugins and themes to crash since they expect
			the user ID to always be valid and have no error checking.

			To mitigate this, the first time we try this (i.e. detectCapsWithNonExistentUser = null),
			we set the setting to `false` and detect capabilities as usual. If execution
			isn't interrupted by a crash, we set it to `true` and don't change it again.

			The user can also manually disable this setting in the "Settings" tab.
			 */
			$moduleSettings = $this->module->loadSettings();
			$detectCapsWithNonExistentUser = $moduleSettings['detectCapsWithNonExistentUser'];
			$doFirstRunTest = (
				($detectCapsWithNonExistentUser === null)
				&& ($moduleSettings instanceof AbstractSettingsDictionary)
			);

			if ( $doFirstRunTest ) {
				$detectCapsWithNonExistentUser = true;
				$moduleSettings->set('detectCapsWithNonExistentUser', false);
				$moduleSettings->save();
			}

			if ( $detectCapsWithNonExistentUser ) {
				//Note: Invalid user ID is intentional. We want a "general" mapping, for someone
				//that's not the author of the post/term or otherwise special. There doesn't seem
				//to be a good way to do that with real users (and there might be no real user that
				//fits those criteria).
				$caps = map_meta_cap($metaCap, 0, $objectId);

				if ( $doFirstRunTest ) {
					//If we got here, it means the test was successful, and we can enable the setting.
					$moduleSettings->set('detectCapsWithNonExistentUser', true);
					$moduleSettings->save();
				}

				return $caps;
			}
		}

		//Either we don't have an object ID or the detectCapsWithNonExistentUser setting is disabled,
		//so we fall back to our simplified mapping.
		return $this->mapMetaCapDirectly($metaCap, $postType, $taxonomy);
	}

	/**
	 * A very simplified version of map_meta_cap() that only handles the meta caps we care about
	 * and doesn't use the current user.
	 *
	 * @param string $metaCap
	 * @param \WP_Post_Type|null $postTypeObject
	 * @param \WP_Taxonomy|null $taxonomy
	 * @return string[]
	 */
	private function mapMetaCapDirectly(
		string         $metaCap,
		?\WP_Post_Type $postTypeObject,
		?\WP_Taxonomy  $taxonomy = null
	): array {
		$othersMetaCaps = [
			'edit_post'   => 'edit_others_posts',
			'delete_post' => 'delete_others_posts',
		];

		switch ($metaCap) {
			case 'read_post':
				if ( $postTypeObject && !empty($postTypeObject->cap->read) ) {
					return [$postTypeObject->cap->read];
				}
				break;
			case 'edit_post':
			case 'delete_post':
				if ( !$postTypeObject ) {
					break;
				}

				if ( !$postTypeObject->map_meta_cap ) {
					if ( !empty($postTypeObject->cap->$metaCap) ) {
						return [$postTypeObject->cap->$metaCap];
					}
					break;
				}

				if ( isset($othersMetaCaps[$metaCap]) ) {
					$othersCap = $othersMetaCaps[$metaCap];
					if ( !empty($postTypeObject->cap->$othersCap) ) {
						return [$postTypeObject->cap->$othersCap];
					}
				}
				break;

			case 'publish_post':
				if ( !$postTypeObject ) {
					break;
				}

				//WordPress doesn't check $postTypeObject->map_meta_cap for "publish_post",
				//it always goes to "publish_posts".
				if ( !empty($postTypeObject->cap->publish_posts) ) {
					return [$postTypeObject->cap->publish_posts];
				}
				break;

			case 'assign_term':
				if ( $taxonomy && !empty($taxonomy->cap->assign_terms) ) {
					return [$taxonomy->cap->assign_terms];
				}
				break;
		}

		return [];
	}

	private function getEnabledPostTypes(): array {
		//It may be possible to replace this with a direct call to $this->module->getEnabledPostTypes()
		//if we don't need additional filtering. Consider changing this if users request support for
		//more post types.

		//Also, the "editor" support check might not really be necessary, I don't have a strong reason
		//for it. It's just a heuristic to exclude "weird" post types that might not work well with
		//this feature. (Note that $module->getEnabledPostTypes() currently also has this check.)

		static $enabledPostTypes = null;
		if ( $enabledPostTypes !== null ) {
			return $enabledPostTypes;
		}

		$enabledPostTypes = [];
		foreach (array_keys($this->module->getEnabledPostTypes()) as $postType) {
			if ( post_type_supports($postType, 'editor') ) {
				$enabledPostTypes[$postType] = $postType;
			}
		}

		return $enabledPostTypes;
	}

	/**
	 * Check if content permissions are enabled for at least one of the given post types.
	 *
	 * @param string|string[] $postTypes Post type name or array of post types.
	 * @return bool
	 */
	public function isSomePostTypeEnabled($postTypes): bool {
		if ( empty($postTypes) ) {
			return false;
		}
		if ( is_string($postTypes) ) {
			$postTypes = [$postTypes];
		} else if ( !is_array($postTypes) ) {
			return false;
		}

		$enabledPostTypes = $this->getEnabledPostTypes();
		foreach ($postTypes as $postType) {
			if ( !empty($enabledPostTypes[$postType]) ) {
				return true;
			}
		}
		return false;
	}

	public function enqueueMetaBoxDependencies() {
		$this->module->enqueuePolicyEditorStyles();

		$uiScript = $this->module->getMetaBoxScript();
		$uiScript->addJsVariable('wsAmeCpeScriptData', [
			'translations' => [
				'tabTitles'         => [
					'basic'      => _x('Basic', 'content permissions tab', 'admin-menu-editor'),
					'advanced'   => _x('Advanced', 'content permissions tab', 'admin-menu-editor'),
					'protection' => _x('Protection', 'content permissions tab', 'admin-menu-editor'),
					'about'      => _x('About', 'content permissions tab', 'admin-menu-editor'),
				],
				'permissionOptions' => [
					'allow'        => _x('Allow', 'content permissions: option label', 'admin-menu-editor'),
					'deny'         => _x('Deny', 'content permissions: option label', 'admin-menu-editor'),
					'default'      => _x('Default', 'content permissions: option label', 'admin-menu-editor'),
					'defaultAllow' => _x('Default: Allow', 'content permissions: option label', 'admin-menu-editor'),
					'defaultDeny'  => _x('Default: Deny', 'content permissions: option label', 'admin-menu-editor'),
				],
				'protectionLabels'  => [
					'replace'      => _x('Show replacement content', 'content permissions: protection type', 'admin-menu-editor'),
					'notFound'     => _x('Show "Not Found" error', 'content permissions: protection type', 'admin-menu-editor'),
					'errorMessage' => _x('Block access', 'content permissions: protection type', 'admin-menu-editor'),
					'redirect'     => _x('Redirect', 'content permissions: protection type', 'admin-menu-editor'),
				],
				'general'           => [
					'noCustomPermissionsReset' => __('No custom permissions to reset.', 'admin-menu-editor'),
				],
			],
		]);
		$uiScript->enqueue();
	}

	/**
	 * @param int $postId
	 * @param \WP_Post $post
	 * @return void
	 * @noinspection PhpMissingParamTypeInspection -- In case WP passes something weird.
	 */
	public function saveMetaBox($postId, $post = null) {
		//Do nothing if content permissions are not enabled for this post type.
		if ( !$post || empty($post->post_type) || !$this->isSomePostTypeEnabled($post->post_type) ) {
			return;
		}

		//Check user permissions.
		if ( !$this->module->userCanEditPolicyForPost($postId) ) {
			return;
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is checked in the method.
		$policy = $this->prepareSubmittedPolicy($_POST, [
			//"view in lists" controls whether the post is visible in the post list in the dashboard,
			//among other things.
			$this->actionRegistry->getAction(ActionRegistry::ACTION_VIEW_IN_LISTS),
			//"edit" is required to edit the post and the associated policy.
			$this->actionRegistry->getAction(ActionRegistry::ACTION_EDIT),
		]);

		if ( !$policy ) {
			return;
		}

		$this->policyStore->setPostPolicy($postId, $policy);
	}

	public function renderTermBox($term) {
		if (
			!($term instanceof \WP_Term)
			//Since terms are added through a separate WP screen, we should always have an ID here.
			//Still, let's do a sanity check in case our assumptions are wrong.
			|| empty($term->term_id)
			//Can the user edit policies for this term? Unlike posts, we don't do this check
			//before adding the UI hook since the term object is not yet available at that point.
			|| !$this->module->userCanEditPolicyForTerm($term->term_id)
		) {
			return;
		}

		$screen = get_current_screen();
		$currentPostType = '';
		if ( $screen && !empty($screen->post_type) ) {
			$currentPostType = $screen->post_type;
		}

		$taxonomy = get_taxonomy($term->taxonomy);
		if ( !($taxonomy instanceof \WP_Taxonomy) ) {
			$taxonomy = null; //get_taxonomy() can return false, but our method signature expects null.
		}
		$postType = !empty($currentPostType) ? get_post_type_object($currentPostType) : null;
		if ( !($postType instanceof \WP_Post_Type) ) {
			//Unlike above, get_post_type_object() returns null on failure, but we still do this check
			//in case it starts returning a different class in the future.
			$postType = null;
		}

		$this->renderPolicyEditorUi(Action::OBJECT_TYPE_TERM, $term, $postType, $taxonomy);
	}

	/**
	 * @param int $termId
	 * @param int $taxonomyId
	 * @param array $args Note: Added in WP 6.1.0, so might be missing in older versions.
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection -- Signature required by the "edited_{$taxonomy}" action.
	 */
	public function saveTermBox($termId, $taxonomyId, $args = []) {
		$term = get_term($termId);
		if ( !($term instanceof \WP_Term) || empty($args) ) {
			return;
		}

		//Check user permissions.
		if ( !$this->module->userCanEditPolicyForTerm($term->term_id) ) {
			return;
		}

		$selectedPostType = '';
		if ( isset($args[self::TERM_POST_TYPE_FIELD_NAME]) ) {
			$selectedPostType = sanitize_text_field(wp_unslash((string)$args[self::TERM_POST_TYPE_FIELD_NAME]));

			//Do nothing if content permissions are not enabled for this post type.
			if ( !$this->isSomePostTypeEnabled($selectedPostType) ) {
				return;
			}
		} else if ( empty($args[self::TERM_UNIVERSAL_POLICY_FIELD_NAME]) ) {
			//If the post type is not specified, the "universal policy" flag must be set.
			return;
		}

		$policy = $this->prepareSubmittedPolicy($args, [
			//Leave posts visible and editable for the current user.
			$this->actionRegistry->getAction(ActionRegistry::ACTION_VIEW_ASSOCIATED_OBJECTS_IN_LISTS),
			$this->actionRegistry->getAction(ActionRegistry::ACTION_EDIT_ASSOCIATED_OBJECTS),
			//Term-level actions are currently not customizable in the term UI, but we include them
			//here in case they are added in the future. They'll just get skipped if the policy
			//doesn't contain any settings for them.
			$this->actionRegistry->getAction(ActionRegistry::ACTION_VIEW_IN_LISTS),
			$this->actionRegistry->getAction(ActionRegistry::ACTION_EDIT),
		]);

		if ( !$policy ) {
			return;
		}

		$this->policyStore->setObjectPolicy(
			Action::OBJECT_TYPE_TERM,
			$term,
			$policy,
			!empty($selectedPostType) ? $selectedPostType : null
		);
	}

	private function prepareSubmittedPolicy(array $args, array $requiredActions): ?ContentItemPolicy {
		//Check nonce.
		if (
			!isset($args[self::BOX_NONCE_NAME])
			//Lots of discussion about if and how nonces should be sanitized, but no clear consensus.
			//See for example https://github.com/WordPress/WordPress-Coding-Standards/issues/869
			|| !wp_verify_nonce(wp_unslash($args[self::BOX_NONCE_NAME]), self::BOX_NONCE_ACTION)
		) {
			return null;
		}

		if ( empty($args[self::POLICY_FIELD_NAME]) ) {
			return null;
		}

		//phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Custom JSON data.
		$policyJson = wp_unslash((string)$args[self::POLICY_FIELD_NAME]);
		$policyData = json_decode($policyJson, true);
		if ( !is_array($policyData) ) {
			return null;
		}

		//Sanitize the replacement text and error message.
		if ( !current_user_can('unfiltered_html') ) {
			if ( isset($policyData['replacementContent']) ) {
				$policyData['replacementContent'] = wp_kses_post((string)$policyData['replacementContent']);
			}

			$errorMessagePath = ['accessProtection', 'protections', 'errorMessage', 'errorMessage'];
			$errorMessage = \ameMultiDictionary::get($policyData, $errorMessagePath);
			if ( !empty($errorMessage) ) {
				\ameMultiDictionary::set($policyData, $errorMessagePath, wp_kses_post((string)$errorMessage));
			}
		}

		$policy = ContentItemPolicy::fromArray($policyData);

		//Try to prevent the user from accidentally creating a policy that blocks themselves from viewing
		//or editing the item. If necessary, enable the relevant actions for at least one of the user's roles.
		//This is not perfect as we don't consider potential cascading permissions from parents or other
		//types of associated items.
		$requiredActions = array_filter($requiredActions);
		$currentUser = $this->actorManager->getCurrentUserActor();

		if ( ($currentUser instanceof User) && !empty($requiredActions) ) {
			$userRoles = $currentUser->getRoleIds();

			//Prioritize admin-like roles.
			$adminLikeRoles = $this->getAdminLikeRoles($userRoles);
			$sortedRoleIds = array_merge($adminLikeRoles, array_diff($userRoles, $adminLikeRoles));
			$sortedRoles = [];
			foreach ($sortedRoleIds as $roleId) {
				$sortedRoles[$roleId] = $this->actorManager->getRole($roleId);
			}

			foreach ($requiredActions as $action) {
				$result = $policy->evaluate($currentUser, $action);
				if ( ($result === null) || !$result->isDenied() ) {
					continue; //The policy doesn't block this action, so we're good.
				}

				//Try to find a role that doesn't already have a custom setting for this action.
				//If the user has multiple roles, they might legitimately want to deny the permission
				//for one of the roles, so we'll try not to override that.
				$chosenRole = null;
				foreach ($sortedRoles as $role) {
					if ( !$policy->hasPermissionSettingFor($role, $action) ) {
						$chosenRole = $role;
						break;
					}
				}

				//Otherwise, just pick the first role.
				if ( !$chosenRole ) {
					$chosenRole = reset($sortedRoles);
				}

				if ( $chosenRole ) {
					$policy->setActorPermission($chosenRole, $action, true);
				}
			}
		}

		return $policy;
	}

	/**
	 * Find the roles that are similar to the "administrator" role in terms of capabilities.
	 *
	 * Note: "names" refers to internal role names/slugs, not display names.
	 *
	 * @param string[]|null $roleNames Optional. If specified, only these roles will be considered.
	 * @return string[] List of role names.
	 */
	private function getAdminLikeRoles(?array $roleNames = null): array {
		$wpRoles = wp_roles();
		if ( $roleNames === null ) {
			$roleNames = array_keys($wpRoles->role_names);
		}

		//A subset of "sufficiently powerful" administrator capabilities. We'll consider a role
		//to be "admin-like" if it has at least one of these capabilities.
		$adminCapsToCheck = [
			'install_plugins',
			'install_themes',
			'delete_plugins',
			'delete_themes',
			'delete_users',
			'edit_plugins',
			'edit_themes',
			'update_core',
			'update_plugins',
			'update_themes',
			'activate_plugins',
			'switch_themes',
			'manage_options',
		];

		$adminLikeRoles = [];
		foreach ($roleNames as $roleName) {
			$role = $wpRoles->get_role($roleName);
			if ( $role ) {
				foreach ($adminCapsToCheck as $cap) {
					if ( !empty($role->capabilities[$cap]) ) {
						$adminLikeRoles[] = $roleName;
						break;
					}
				}
			}
		}

		//Always include the "administrator" role, if it exists.
		if ( in_array('administrator', $roleNames, true) && !in_array('administrator', $adminLikeRoles, true) ) {
			$adminLikeRoles[] = 'administrator';
		}

		return $adminLikeRoles;
	}
}