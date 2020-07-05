app.factory('PartSvc', function(RequestSvc) {

    var model = 'part';

    return {
        index: function(params) {
            return RequestSvc.get('/api/' + model + '/index', params);
        },
        read: function(id) {
            return RequestSvc.get('/api/' + model + '/read/' + id);
        },
        save: function(params) {
            return RequestSvc.post('/api/' + model + '/save', params);
        },
        saveIt: function(params) {
            return RequestSvc.post('/api/' + model + '/save-it', params);
        },
        remove: function(params) {
            return RequestSvc.post('api/' + model + '/delete', params);
        },
        options: function(params) {
            return RequestSvc.get('/api/' + model + '/options', params);
        },
    };

});