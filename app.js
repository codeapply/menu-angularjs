function MenuController($scope, $http) {

    //Retrieve menu data
    $http.get('menuitems.json').
        success(function(data) {
            $scope.items = data;
        }).
        error(function(data,status) {
            console.log("status: "+status);
        });

    // Defaults
    $scope.sublinks = null;
    $scope.activeItem = null;

    // Default submenu left padding to 0
    $scope.subLeft = {'padding-left':'0px'};

    /*
     * Set active item and submenu links
     */
    $scope.showSubMenu = function(item,pos) {
        // Move submenu based on position of parent
        $scope.subLeft = {'padding-left':(80 * pos)+'px'};
        // Set activeItem and sublinks to the currectly
        // selected item.
        $scope.activeItem = item;
        $scope.sublinks = item.sublinks;
    };
    
}

