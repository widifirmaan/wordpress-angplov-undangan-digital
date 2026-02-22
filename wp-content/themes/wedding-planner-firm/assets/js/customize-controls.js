( function( api ) {

	// Extends our custom "wedding-planner-firm" section.
	api.sectionConstructor['wedding-planner-firm'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

} )( wp.customize );