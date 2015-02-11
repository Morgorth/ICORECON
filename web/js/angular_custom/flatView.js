var FlatView = angular.module('FlatView', ['ui.router','ngResource','xeditable','GlobalUtilsFunctionsCheckInput']);


// --------------------------------------------------- ROUTING  ------------------------------------------------

FlatView.config(function($stateProvider, $urlRouterProvider) {

  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/");
  
  
  // Now set up the states
  $stateProvider
    .state('flatView', {
      url: "",
      controller:"flatViewHome",
      templateUrl:Routing.generate('_NRtworks_FlatView_elementList')                
    });

});


// ----------------------------------------------------- DATA ----------------------------------------------------

FlatView.service('reUsableData',['$http','$q',function($http,$q){

    var deferred = $q.defer();
     $http({method:'POST',data:{dimensionpassed:dimension},url:Routing.generate('_NRtworks_FlatView_API_getData')}).success(function(result){
         deferred.resolve(result); 
      });                
      
     return deferred.promise;
                  
    
}]);

FlatView.service('dataService',['$http',function($http){
     
    this.getData = function () {
            return $http({method:'POST',data:{dimensionpassed:dimension},url:Routing.generate('_NRtworks_FlatView_API_getData')});
        };
        
    this.saveData = function(JSONToSend){
            return $http.post(Routing.generate('_NRtworks_FlatView_API_saveData'),{postContent:JSONToSend});
    };    
     
}]);


// ----------------------------------------------------  main controler -------------------------------------------

FlatView.controller('flatViewHome',['reUsableData','dataService','$scope','$filter','GlobalUtilsFunctionsCheckInputMethods','$q',function (reUsableData,dataService,$scope,$filter,GlobalUtilsFunctionsCheckInputMethods,$q)
{
   //we get the data that is shareable  between the views
    reUsableData.then(function(result){
        $scope.dimension = result["dimension"];
        $scope.data = result["elementList"];
        $scope.fieldsParameters = result["fieldsParameters"];
        $scope.defaultObject = result["defaultObject"];
        $scope.nbFields = result["nbFields"];
        $scope.defaultObject[result["nbFields"]] = "NRtworks_FlatView_T0Cr3at3";
    });
    //we get the constraints related to the fields
    GlobalUtilsFunctionsCheckInputMethods.getConstraints().then(function(result){
        $scope.propertiesConstraints = result["propertiesConstraints"];
        
    });


    //the function that manages the data service get tree
    function reloadData() 
    {
        dataService.getData()
            .success(function (result) {
                $scope.dimension = result["dimension"];
                $scope.data = result["elementList"];
                $scope.fieldsParameters = result["fieldsParameters"];
                $scope.defaultObject = result["defaultObject"];
                $scope.nbFields = result["nbFields"];
                $scope.defaultObject[result["nbFields"]] = "NRtworks_FlatView_T0Cr3at3";
            })
            .error(function (error) {
                $scope.status = 'Unable to load customer\'s data: ' + error.message;
            });
    }

    //the function that manages the data service set tree
    function serviceSaveData(JSONToSend){
        dataService.saveData(JSONToSend)
                .success(function (res) 
                {
                    res = res || {};
                    console.log(res);
                    //reloading the tree from the DB
                    dataService.getData()
                    .success(function (result) 
                    {
                        $scope.dimension = result["dimension"];
                        $scope.data = result["elementList"];
                        $scope.fieldsParameters = result["fieldsParameters"];
                        $scope.defaultObject = result["defaultObject"];
                        $scope.nbFields = result["nbFields"];
                        $scope.defaultObject[result["nbFields"]] = "NRtworks_FlatView_T0Cr3at3";                        
                        
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


    // FRONT modification functions

    // variables set  to initiate the sorting funtion
    $scope.orderByField = 0;
    $scope.orderByFieldName = "id";
    $scope.reverseSort = false; 
 
    $scope.reload = function()
    {
        reloadData();
    }
    
    $scope.saveChanges = function ()
    {
        console.log($scope.data);
        var dataToBeSaved = [];
        dataToBeSaved[0] = $scope.dimension;
        dataToBeSaved[1] = $scope.data;
        //var test = JSON.stringify($scope.data);
        //console.log(dataToBeSaved[1]);
        var JSONToSend = JSON.stringify(dataToBeSaved);
        console.log(JSONToSend)
        //console.log(JSONToSend);
        serviceSaveData(JSONToSend);        
    }
 
    //the following function is used to build a select element
    $scope.selectElement = function(element,index) {
        var selected = [];
        if(element) {
          selected = $filter('filter')($scope.fieldsParameters[index]["options"],{value: element});
          if(typeof selected == 'array'){return selected[0].text;}else{return "Not set"}
        }
        return "not set";
    };
 
    $scope.addNewElement = function()
    {
        var topush  = NRtworksUtilsFunctions.cloneObject($scope.defaultObject);
        $scope.data.push(topush);
        
        console.log($scope.data);
    }
 
    $scope.removeElement = function (index,element)
    {
        if(element[$scope.nbFields])
        {
            if(element[$scope.nbFields] == "NRtworks_FlatView_T0Cr3at3")
            {
                $scope.data.splice(index,1);
            }
        }
        else
        {
            if(element[$scope.nbFields+1])
            {
                if(element[$scope.nbFields+1] === "NRtworks_FlatView_ToD3l3t3")
                {
                    element.splice([$scope.nbFields+1],1)
                }
                else
                {
                    element[$scope.nbFields+1] = "NRtworks_FlatView_ToD3l3t3";
                }
            }
            else
            {
                element[$scope.nbFields+1] = "NRtworks_FlatView_ToD3l3t3";
            }
        }
        console.log(element);
    }

    $scope.checkInput = function(data,property,dimension,fieldName) 
    {   
        
        if(data == property)
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



FlatView.directive('selectWhenEditing',function(){
    var linkFunction = function(scope,startingelem,attr)
    {
       
        startingelem.on('click',function(event){            
            if(startingelem[0].querySelector('.editable-input')!== null)
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