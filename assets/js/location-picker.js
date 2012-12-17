/* =======================================================
 * location-picker.js
 * 
 * ===================================================== */

!function ($) {
  var LocationPicker = function(element, options) {
    this.options = options
    this.$element = $(element)
  }
  
  LocationPicker.prototype = {
      constructor: LocationPicker
    , init: function() {
        this.$province = $('#' + this.options.provinceId, this.$element)
        this.$city = $('#' + this.options.cityId, this.$element)
        this.$district = $('#' + this.options.districtId, this.$element)
        this.$input = $('#' + this.options.inputId, this.$element)
      }
    , run: function() {
        var _this = this
        this.init();
        
        this.$province.on('change', function() {
          var value = $(this).val()
          _this.replace(_this.$city, _this.cityList(value))
          _this.replace(_this.$district, {})
          _this.writeOut(value)
          
        })
        
        this.$city.on('change', function() {
          var value = $(this).val()
          _this.replace(_this.$district, _this.districtList(value))
          _this.writeOut(value)
          if(!_this.cityHasDistrict(value)) {
            _this.$district.hide()
          }
        })
        
        this.$district.on('change', function() {
          _this.writeOut($(this).val())
        })
      }
    , provinceList: function() {
        return $.LocationData.province
      }
    , cityList: function(provinceId) {
        return $.LocationData.city[provinceId]
      }
    , districtList: function(cityId) {
        return $.LocationData.district[cityId]
      }
    , cityHasDistrict: function(cityId) {
        return $.LocationData.district[cityId] ? true : false
      }
    , replace: function(object,newList) {
        object.empty().show()
        
        object.append('<option value="0" selected>--请选择--</option>')
        for(key in newList) {
          object.append('<option value="' + key + '">' + newList[key] + '</option>')
        }
      }
    , writeOut: function(value) {
      this.$input.val(value)
    }
  }
  
  $.fn.LocationPicker = function (option) {
    return this.each(function(){
      var $this = $(this)
       , options = $.extend($.fn.LocationPicker.defaults, option)
       , picker = new LocationPicker($this, options)
       console.dir(options)
      picker.run();
    })
  }
  
  $.fn.LocationPicker.Constructor = LocationPicker
  
  $.fn.LocationPicker.defaults = {
      provinceId: 'province'
    , cityId: 'city'
    , districtId: 'district'
    , inputId: 'location'
  }
}(window.jQuery)
