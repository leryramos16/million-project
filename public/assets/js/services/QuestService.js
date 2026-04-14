app.service('QuestService', function($http) {

    this.getQuests = function() {
        return $http.get('/mymillionpesoproject/public/questapi')
    };
});