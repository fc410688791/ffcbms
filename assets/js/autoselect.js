/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var AutoSelect = function(config){
     this.config = config;
     this._clean = function(index){
         for(var i =0;i< this.config.length;i++){
              if(index <=  this.config[i].index || !this.config[i].clean){
                  continue;
              }
              var txt = "<option value=''>"+ this.config[i].val+"</option>";
              $("#"+ this.config[i].key).html(txt);
         }
     };
};
AutoSelect.prototype.bind = function(elem){
      var self = this;
      $(elem).on("change",function(){
         var $this = $(this);
         var this_id = $this.attr("id");
         var elem_config = {};
         for(var i =0;i<self.config.length;i++){
                  if(self.config[i].key == this_id){
                      elem_config = self.config[i];
                      elem_config.data['p'] = $this.val();
                      elem_config.data['s'] = $this.val();
                      if(self.config[i].key == "station_type"){
                          elem_config.data['type_id'] = $this.val();
                          elem_config.data['station_addr'] = $("#city").find("option:selected").val();
                          elem_config.data['p']  = $("#province").find("option:selected").val();
                      }
                      if(self.config[i].key == "city"){
                          elem_config.data['type_id'] = $("#station_type").find("option:selected").val();
                          elem_config.data['station_addr'] = $this.val();
                      }
                  }                  
         }
         self._clean(elem_config.index);
         if(elem_config.url !== ""){
            $.ajax({
               url:elem_config.url,
               dataType:'json',
               data:elem_config.data,
               success:function(res){
                    var thtml = "<option value=''>选择全部</option>";
                    for(var i = 0;i<res.data.length;i++)
                    {
                           thtml+='<option value="' + res.data[i]['id'] + '">' + res.data[i]['name'] +'</option>';
                    } 
                    $("#"+elem_config.elemfor).html(thtml);
                    if($("#"+elem_config.key).val() == ""){
                       if(elem_config.hasOwnProperty("nextdef")){
                             $.ajax(elem_config.nextdef);
                       }
                    }
               }
            });
         }
      });
};
