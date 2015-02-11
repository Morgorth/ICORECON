
angular.module('GlobalUtilsFunctionsCheckInput',['ngResource'])

// --------------------------------------------------- data services  ------------------------------------------------

.service('dataServiceConstraintsValidator',['$http','$q',function($http,$q){
     
    this.checkInDB = function (dimension,fieldName,data) {
        var deferred = $q.defer();
        $http({method:'POST',data:{dimensionPassed:dimension,fieldPassed:fieldName,dataPassed:data},url:Routing.generate('_NRtworks_globalUtilsFunctions_checkInDB')})
        .success(function(result)
        {
            deferred.resolve(result); 
        })
        .error(function(error)
        {
          deferred.reject(error.msg);
        });                
      
     return deferred.promise;
        
        //return $http({method:'POST',data:{dimensionPassed:dimension,fieldPassed:fieldName,dataPassed:data},url:Routing.generate('_NRtworks_globalUtilsFunctions_checkInDB')});
        };
    this.getConstraints = function (dimension) {
            return $http({method:'POST',data:{dimensionpassed:dimension},url:Routing.generate('_NRtworksK_globalUtilsFunctions_validatorGetConstraints')});
        };        
             
}])

.service('dataServiceGetConstraints',['$http','$q',function($http,$q){

    var deferred = $q.defer();
     $http({method:'POST',data:{dimensionpassed:dimension},url:Routing.generate('_NRtworks_globalUtilsFunctions_validatorGetConstraints')}).success(function(result){
         deferred.resolve(result); 
      }).error(function(error){
          deferred.reject(error.msg);
      });                
      
     return deferred.promise;
                  
    
}])

// --------------------------------------------------- METHODS  ------------------------------------------------

.factory('GlobalUtilsFunctionsCheckInputMethods', function (dataServiceConstraintsValidator,dataServiceGetConstraints) {
    return{        
        
        //the following function is made to allow other angular module to reach the data service
        getConstraints:function getConstraints()
        {
            return dataServiceGetConstraints; 
        } 
        ,checkUnicity:function checkUnicity(dimension,fieldName,data)
        {
            return dataServiceConstraintsValidator.checkInDB(dimension,fieldName,data);
        }
        //the following function is the validation's start. Checking if a constraint exists for this field and calling the necessary function
        ,check:function check(constraints,fieldName,data) 
        {
            var $this = this;
            
            if(constraints != "free")
            {
                console.log(fieldName);
                console.log(Object.keys(constraints));
                console.log(Object.keys(constraints).indexOf(fieldName));
                
                if(Object.keys(constraints).indexOf(fieldName)>=0)
                {
                    console.log("we have constraints for this field");
                    console.log(constraints[fieldName]);
                    //for each constraint, we call the function that will validate it
                    //and also an array result is built to be returned
                    var status = 1;
                    
                    var i = 0;
                    for(var constraint in constraints[fieldName])
                    {
                        var next = $this.validateConstraint(constraint,constraints[fieldName][constraint],fieldName,data);
                        //console.log(next);
                        console.log(typeof next);
                        if(typeof next === "string")
                        {
                            console.log("constraint is broken");
                            status = next;
                            break;
                           
                        }
                        else 
                        { 
                            console.log("constraint isn't broken");
                        }
                    }
                    return status;
                }
            }
            else
            {
                return 1;
            }
            

        }        
        ,validateConstraint: function validateConstraint(constraintName,constraint,fieldName,data)
        {
            console.log("single constraint validation");
            console.log(constraintName);
            console.log(constraint);
            switch(constraintName)
            {
                case "NotBlank":
                    if(data == null || data == ""){return constraint[1];}else{ return 1}
                    break;
                case "Length":
                    if(data.length < constraint[0][0])
                    {
                        return constraint[1][0];
                    }
                    if(data.length > constraint[0][1])
                    {
                        return constraint[1][1];
                    }
                    else
                    {
                        return 1
                    }
                    break;
                case "Regex":
                    console.log(constraint[0]);
                    var myRegex = new RegExp(constraint[0]); 
                    console.log(myRegex);
                    if(myRegex.test(data))
                    {
                        return 1;
                    }
                    else
                    {
                        return constraint[1];
                    }
                    
                    break;
                default:
                    console.log("unknown constraint");
                    break;
            }
        }
        

        // the following function is the one that will validate if the data is valid given the Unique constraint
        ,unique: function(dimension,fieldName,data,constraints)
        {
            console.log("checking unicity constraints");
            if(constraints != "free")
            {
                var $this = this;

                dataServiceConstraintsValidator.checkInDB(dimension,fieldName,data)
                .success(function (res) 
                {
                    console.log("answered");
                    if(res.status === 1)
                    {
                        return 1;
                    }
                    if(res.status === 0)
                    {
                        console.log(res.errorMessage);
                        return res.errorMessage;
                    }
                })
                .error(function(error) 
                {
                    console.log("Error of the $http element");
                });
            }
            else
            {
                return 1;
            }
        }


    };
});

