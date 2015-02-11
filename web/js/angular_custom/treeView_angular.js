newAccounts = 1;



var treeView = angular.module('treeView', ['ngResource','xeditable','ui.tree','ui.router','GlobalUtilsFunctionsCheckInput']);


// --------------------------------------------------- LOADING DATA ------------------------------------------------

treeView.service('reUsableData',['$http','$q',function($http,$q){

    var deferred = $q.defer();
     $http({method:'POST',data:{dimensionpassed:dimension},url:Routing.generate('_API_getTree')}).success(function(result){
         deferred.resolve(result); 
      });                
      
     return deferred.promise;
                  
    
}]);

treeView.service('dataService',['$http',function($http){
     
    this.getTree = function () {
            return $http({method:'POST',data:{dimensionpassed:dimension},url:Routing.generate('_API_getTree')});
        };
        
    this.saveTree = function(JSONToSend){
            return $http.post(Routing.generate('_API_saveTree'),{postContent:JSONToSend});
    };    
     
}]);

// --------------------------------------------------- ROUTING  ------------------------------------------------

treeView.config(function($stateProvider, $urlRouterProvider) {

  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/");
  
  
  // Now set up the states
  $stateProvider
    .state('treeView', {
      url: "",
      controller:"treeViewMain",
      templateUrl:Routing.generate('_NRtworks_treeView_callEditTemplate')                
    })
    .state('fullEdit',{
        url:"/fullEdit/:elementId",
        templateUrl: Routing.generate('_NRtworks_treeView_callFullEditTemplate'),
        controller:"fullEdit"                            
    });

});

// --------------------------------------------------- CONTROLLERS  ------------------------------------------------

treeView.controller('treeViewMain',['$scope','$state','$http','dataService','reUsableData','$q','GlobalUtilsFunctionsCheckInputMethods',function ($scope,$state,$http,dataService,reUsableData,$q,GlobalUtilsFunctionsCheckInputMethods)
{    

    //we get the data that is shareable  between the views
    reUsableData.then(function(result){
        $scope.dimension = result["dimension"];
        $scope.data = result["tree"];   
        $scope.highest = result["highestID"];
        $scope.defaultObject = result["defaultObject"];
                
        //console.log($scope.defaultObject);
    });
    
    //the function that manages the data service get tree
    function serviceGetTree() {
        dataService.getTree()
            .success(function (result) {
                $scope.dimension = result["dimension"];
                $scope.data = result["tree"];    
                $scope.highest = result["highestID"];
                $scope.defaultObject = JSON.parse(result["defaultObject"]);
            })
            .error(function (error) {
                $scope.status = 'Unable to load customer data: ' + error.message;
            });
    }

    //the function that manages the data service set tree
    function serviceSaveTree(JSONToSend){
        dataService.saveTree(JSONToSend)
                .success(function (res) 
                {
                    res = res || {};
                    console.log(res);
                    //reloading the tree from the DB
                    dataService.getTree()
                    .success(function (result) 
                    {
                        $scope.dimension = result["dimension"];
                        $scope.data = result["tree"];    
                        $scope.highest = result["highestID"];
                        $scope.defaultObject = result["defaultObject"];
                        
                        if(res.status === 'ok') 
                        {                        
                        } 
                        else 
                        { 
                            res.failed.forEach(function($failedElement)
                            {

                              if($failedElement.action === "new")
                              {   
                                  $failedElement.nodes = [];
                                  console.log(NRtworksUtilsFunctions.findById($scope.data,$failedElement.parent_id));
                                  (NRtworksUtilsFunctions.findById($scope.data,$failedElement.parent_id)).nodes.push($failedElement);
                              }
                              else
                              {   
                                  var oldElement = NRtworksUtilsFunctions.findById($scope.data,$failedElement.id);
                                  if($failedElement.to_delete=="yes")
                                  {
                                      oldElement.to_delete="yes";
                                  }

                                   NRtworksUtilsFunctions.updateElement(utils.transformArray($failedElement),oldElement,"modified");
                              }                
                            });

                        } 
                    });
                   
                })
                .error(function(error) 
                {
                    console.log("Error of the $http element");
                });
    }
    
    //the function that buils the tree to be sent to back end
    $scope.saveChanges = function(){
        
        var dataToBeSaved = [];
        dataToBeSaved[0] = $scope.dimension;
        dataToBeSaved[1] = $scope.data;
        //var test = JSON.stringify($scope.data);
        //console.log(dataToBeSaved[1]);
        var JSONToSend = JSON.stringify(dataToBeSaved);
        //console.log(JSONToSend);
        serviceSaveTree(JSONToSend);
    };

    // function to go to full edit when dbl click on an element,passing this element to the new frame
    $scope.goToFullEdit = function(elementToEdit){
       $state.go('fullEdit', { element: "elementToEdit" }); 
    };

    // the following function is the one that adds a new element to the model
    // the added element is the default one gotten from the back end
    $scope.newSubItem = function(scope) 
    {
        var nodeData = scope.$modelValue;
        console.log(scope.$modelValue);
        var topush = NRtworksUtilsFunctions.cloneObject($scope.defaultObject);
        if(topush !== 0)
        {
            topush.id = topush.id + newAccounts;
            console.log(topush);
            nodeData.nodes.push(topush);
        }
        newAccounts++;
        //try
    };
    
    
    // the following function is the delete (or un-delete function)
    // if the node is marked to be deleted, it will un-marked it
    // otherwise, if the node isn't yet in the DB it will remove it from the scope
    // last case, the function marks the node to be deleted
    $scope.removeElement = function(scope,node){
        
        console.log(node);  
        if(node.to_delete === "yes")
        {
            // this is actually the un-delete function
            node.to_delete = "";
            NRtworksUtilsFunctions.deleteChildren(node,"");
        }
        else
        {
            if(node.action==="new")
            {
                scope.remove();
            }
            else
            {            
                node.to_delete = "yes";  
                NRtworksUtilsFunctions.deleteChildren(node,"yes");
            }  
        }
    };

    // below are the functions from the tree library
    $scope.toggle = function(scope) {
      scope.toggle();
    };

    $scope.moveLastToTheBegginig = function () {
      var a = $scope.data.pop();
      $scope.data.splice(0,0, a);
    };
    
    var getRootNodesScope = function() {
      return angular.element(document.getElementById("tree-root")).scope();
    };

    $scope.collapseAll = function() {
      var scope = getRootNodesScope();
      scope.collapseAll();
    };

    $scope.expandAll = function() {
      var scope = getRootNodesScope();
      scope.expandAll();
    };  
    
   
}]);


// ---------------- CONTROLLER : full edit view

treeView.controller('fullEdit',['$scope','$stateParams','$state','reUsableData','$filter','$q', 'GlobalUtilsFunctionsCheckInputMethods',function ($scope,$stateParams,$state,reUsableData,$filter,$q,GlobalUtilsFunctionsCheckInputMethods)
{    
    reUsableData.then(function(result){
        $scope.data = result["tree"];
        $scope.fieldsSettings = result["fieldsSettings"];
        $scope.dimension = result["dimension"];
        
        var element = NRtworksUtilsFunctions.findById($scope.data,$stateParams.elementId);
        //console.log("start editing:old element:");
        //console.log(element);
        element = NRtworksUtilsFunctions.transformArray(element);    
        $scope.elementToEdit = element;
        
        
    }); 
    
    GlobalUtilsFunctionsCheckInputMethods.getConstraints().then(function(result){
        $scope.propertiesConstraints = result["propertiesConstraints"];
        
    });
    
    
    //the following function is used to build a select element
    $scope.selectElement = function(element,index) {
        var selected = [];
        if(element) {

          selected = $filter('filter')($scope.fieldsSettings[index][3],{value: element});
        }
        return selected.length ? selected[0].text : 'Not set';
      };


    $scope.seeTheTree = function()
    {
        $state.go('treeView');
    };
    
    //the following function copies the edited element into the original array of data
    $scope.saveElement = function (element)
    {       
        var status = "";
        var oldElement = NRtworksUtilsFunctions.findById($scope.data,element[0].prop);
        //console.log("edited:oldElement:");
        //console.log(oldElement);
        if(oldElement.to_delete === "yes" || oldElement.action === "new")
        {
            status = "not";
        }
        else
        {
            status = "modified";
        }        
        
        var confirm = NRtworksUtilsFunctions.updateElement(element,oldElement,status); 
        //console.log("end edition:elementinscope:");
        //console.log(confirm);
        
    };
    
    $scope.checkInput = function(data,property,dimension,fieldName) 
    {   
        
        if(data == property.prop)
        {
            return null;
        }
        else
        {
            var deferred = $q.defer();

            GlobalUtilsFunctionsCheckInputMethods.getConstraints().then(function(result){
                //we get the constraints
                var constraints = result["propertiesConstraints"];
                var unicityConstraints = result["entityConstraints"];
                
                var status = GlobalUtilsFunctionsCheckInputMethods.check(constraints,fieldName,data);
                console.log("back in treeView");
                
                
                //ok now we'll check the unicity constraint, only if the previous constraints check is proven true
                if(unicityConstraints && status == 1)
                {
                    GlobalUtilsFunctionsCheckInputMethods.checkUnicity(dimension,fieldName,data).then(function(res)
                    {
                        console.log("answered");
                        if(res.status === 1)
                        {
                            deferred.resolve(res.status);
                        }
                        if(res.status === 0)
                        {
                            console.log(res.errorMessage);
                            deferred.resolve(res.errorMessage);
                        }
                        
                        
                    });
                }
                else
                {
                    deferred.resolve(status);
                }
                
                
                
            });
            
            return deferred.promise;
            

        }
    };    
    

               
}]);



// --------------------------------------------------- VARIOUS DIRECTIVES  ------------------------------------------------


treeView.directive('selectWhenEditing',function(){
    var linkFunction = function(scope,startingelem,attr)
    {
       
        startingelem.on('click',function(event){            
            if(startingelem[0].querySelector('.editable-input')!= null)
            {
                var el = startingelem[0].querySelector('.editable-input');
                if (el.tagName == "INPUT") {
                    el.select();
                }
            }
        });
        
    };
    return{
        restrict: 'AEC',
        link:linkFunction
    };}
);

