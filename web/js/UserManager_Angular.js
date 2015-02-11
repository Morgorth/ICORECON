var UserManager = angular.module('UserManager', ['ngRoute','ngResource','xeditable']);

UserManager.config(function($routeProvider){
   $routeProvider 
   .when('/addnew',
   {
       controller:'addNewController',
       templateUrl:Routing.generate('_UserManager_addNewUser')
   })
   .when('/',
   {
       controller:'UserManagerHome',
       templateUrl:Routing.generate('_UserManager_getUser_List')
   });
   
});


UserManager.factory('API_data_userList',['$resource', 
    function($resource){
    return $resource(Routing.generate('_UserManager_API_getUserList'),{},{
        query:{method:'GET',isArray:true}
    });
    
}]);

UserManager.factory('API_test',['$http','$q',function($http,$q){
    var getData = function(){
      var deferred = $q.defer();
      $http({method:'GET',url:Routing.generate('_UserManager_API_getUserList')}).success(function(result){
         deferred.resolve(result); 
      });                
      return deferred.promise;  
    };
    return { getData: getData};
}]);

UserManager.factory('API_data_userTypeList',['$resource', 
    function($resource){
    return $resource(Routing.generate('_UserManager_API_getUserTypeList'),{},{
        query:{method:'GET',isArray:true}
    });
    
}]);

UserManager.controller('UserManagerHome',function ($scope,API_data_userList,API_data_userTypeList,API_test,$filter,$http,$q)
{
    //getting necessary data
    $scope.users = API_data_userList.query();     
    $scope.types = API_data_userTypeList.query();

    //function used to show the data
    $scope.showStatus = function(user) {        
    var selected = [];    
    if(user.type) {
      
      selected = $filter('filter')($scope.types,  {id: user.type.id});
      
    }
   
    return selected.length ? selected[0].name : 'Not set';
  };

    
    //sorting
    $scope.orderByField = 'firstName';
    $scope.reverseSort = false;
     
    // Delete a User
    $scope.removeUser = function(index,user) {
        if(user.id==="new")
        {
            $scope.users.splice(index, 1);
        }
        else
        {
            $scope.users[index].to_delete = "yes";
        }
    };
    //Undelete a user
    $scope.unDeleteUser = function(index) {
          //$scope.users.splice(index, 1);
          delete $scope.users[index]['to_delete'];
    };
        
    // add user
    $scope.addUser = function() {
      $scope.inserted = {
          
        id:"new",
        username: 'username',
        email: 'email@mycompany.com',
        type: {'id':$scope.types[0].id,"name":$scope.types[0].name,"symfonyRole":$scope.types[0].symfonyRole}
        
      };     
      $scope.users.push($scope.inserted);
    };
    
    $scope.saveChanges = function(){
        var d = $q.defer();
        $http.post(Routing.generate('_UserManager_API_saveChanges'), $scope.users).success(function(res) 
        {
            res = res || {};
            if(res.status === 'ok') 
            { 
              //when all the users are updated correctly, we can reload the data  
              d.resolve();
              $scope.users = API_data_userList.query();
            } 
            else 
            { 
              
              // if not everyhting was saved, we load the data and re-modify the failed elements  
              d.resolve(res.msg);
              var myDataPromise = API_test.getData();
              myDataPromise.then(function(result)
              {       
                  $scope.users = result;
                  res.failed.forEach(function($caller)
                  {
                    if($caller.id === "new")
                    {     
                          $scope.users.push($caller);
                    }
                    else
                    {
                        
                        var old_user = $filter('filter')($scope.users,  {email: $caller.email});
                        var index = $scope.users.map(function(e){return e.email}).indexOf($caller.email);
                        $scope.users[index] = old_user;
                    }                
                  });
              });
              
            }
        }).error(function(e)
            {
                d.reject('Server error!');
            });
            
        return d.promise;
    
        
    };
    
    
    
    
    // -------------- user entered data checking functions ----------------
    $scope.checkUsername = function(data,user) {    
    if(user.username == data)
    {
        
    }
    else
    {
        var d = $q.defer();
        $http.post(Routing.generate('_UserManager_API_checkIfUsernameExists'), {value: data}).success(function(res) {
          res = res || {};
          if(res.status === 'ok') { 
            d.resolve();
          } else { 
            d.resolve(res.msg);
          }
        }).error(function(e){
          d.reject('Server error!');
        });
        return d.promise;
        };
    };
    
    
});

UserManager.controller('addNewController',function ($scope,data_access){
    $scope.users = data_access.query();    
    //$scope.orderProp = 'id';
    
});


UserManager.directive('addNewUserForm',function(){
   return{
       restrict:'AE',
       replace:true,
       templateUrl:Routing.generate('_UserManager_form_addNewUser')
       
   }; 
});



UserManager.directive('selectWhenEditing',function(){
    
    var linkFunction = function(scope,startingelem,attributes)
    {
        console.log(startingelem[0].querySelector('.editable'));
        angular.element(startingelem[0].querySelector('.editable')).on('click',function()
        {     
            console.log("test");
            angular.element(startingelem[0].querySelector('.editable-input').select());            
        });
        
    };
    
    return{
        restrict: 'AEC',
        link:linkFunction
    };
});