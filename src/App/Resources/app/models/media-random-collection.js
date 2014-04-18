define([ 'app/globals', 'app/models/base/collection', 'app/models/media-model', 'chaplin' ], function (Globals, BaseCollection, MediaModel, Chaplin) {
    'use strict';

    var MediaRandomCollection = BaseCollection.extend({
	    model: MediaModel,
	    url: function() {
			    return 'https://api.instagram.com/v1/media/popular?count=12&client_id=' + Globals['instagram-client-id'];
	    },
	    parse: function(response) {

		    if (response.meta && response.meta.code === 200) {
				console.log('loaded', response.data);
			    return response.data;
		    }

		    return [];
	    }
    });

	return MediaRandomCollection;
});