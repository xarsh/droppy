define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'model/event'
], function($, _, Backbone, Event){
	var EventList = Backbone.Collection.extend({
		model : Event,
		
		initialize : function() {
			this.allLoaded = false;
			this.loading = false;
		},
		
		setFromJSON : function(json) {
			for(var i in json) {
				var event = new Event;
				event.setFromJSON(json[i]);
				this.add(event, { silent : true });
			}
			this.trigger('reset');
		},
		
		comparator : function(event, other) {
			return -1 * event.compareDates(other);
		},
		
		loadMore : function() {
			if(this.loading || this.allLoaded) {
				return;
			}
			this.params = { offset : this.length };
			this.loading = true;
			var self = this;
			this.fetch({
				add : true,
				success : function(method, model) {
					self.loading = false;
					if(model.length === 0) {
						self.allLoaded = true;
					}
				}
			});
		},
		
		sync : function(method, model, options) {
			var params = ('params' in this) ? this.params : {}; 
			options.url = Routing.generate(this.routeList[method], params);
			return Backbone.sync(method, model, options);
		}
	});
	
	EventList.fromJSON = function(jsonDatas) {
		return _.map(jsonDatas, function(data) {
			var event = new Event;
			event.setFromJSON(data);
			return event;
		});
	};
  return EventList;
});

