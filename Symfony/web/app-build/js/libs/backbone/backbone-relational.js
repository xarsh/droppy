/**
 * Backbone-relational.js 0.5.0
 * (c) 2011 Paul Uithol
 * 
 * Backbone-relational may be freely distributed under the MIT license.
 * For details and documentation: https://github.com/PaulUithol/Backbone-relational.
 * Depends on Backbone: https://github.com/documentcloud/backbone.
 */

(function(e){var t,n,r;typeof window=="undefined"?(t=require("underscore"),n=require("backbone"),r=module.exports=n):(t=window._,n=window.Backbone,r=window),n.Relational={showWarnings:!0},n.Semaphore={_permitsAvailable:null,_permitsUsed:0,acquire:function(){if(this._permitsAvailable&&this._permitsUsed>=this._permitsAvailable)throw new Error("Max permits acquired");this._permitsUsed++},release:function(){if(this._permitsUsed===0)throw new Error("All permits released");this._permitsUsed--},isLocked:function(){return this._permitsUsed>0},setAvailablePermits:function(e){if(this._permitsUsed>e)throw new Error("Available permits cannot be less than used permits");this._permitsAvailable=e}},n.BlockingQueue=function(){this._queue=[]},t.extend(n.BlockingQueue.prototype,n.Semaphore,{_queue:null,add:function(e){this.isBlocked()?this._queue.push(e):e()},process:function(){while(this._queue&&this._queue.length)this._queue.shift()()},block:function(){this.acquire()},unblock:function(){this.release(),this.isBlocked()||this.process()},isBlocked:function(){return this.isLocked()}}),n.Relational.eventQueue=new n.BlockingQueue,n.Store=function(){this._collections=[],this._reverseRelations=[],this._subModels=[],this._modelScopes=[r]},t.extend(n.Store.prototype,n.Events,{addModelScope:function(e){this._modelScopes.push(e)},addSubModels:function(e,t){this._subModels.push({superModelType:t,subModels:e})},setupSuperModel:function(e){t.find(this._subModels,function(n){return t.find(n.subModels,function(t,r){var i=this.getObjectByName(t);if(e===i)return n.superModelType._subModels[r]=e,e._superModel=n.superModelType,e._subModelTypeValue=r,e._subModelTypeAttribute=n.superModelType.prototype.subModelTypeAttribute,!0},this)},this)},addReverseRelation:function(e){var n=t.any(this._reverseRelations,function(n){return t.all(e,function(e,t){return e===n[t]})});if(!n&&e.model&&e.type){this._reverseRelations.push(e);var r=function(e,n){e.prototype.relations||(e.prototype.relations=[]),e.prototype.relations.push(n),t.each(e._subModels,function(e){r(e,n)},this)};r(e.model,e),this.retroFitRelation(e)}},retroFitRelation:function(e){var t=this.getCollection(e.model);t.each(function(t){if(!(t instanceof e.model))return;new e.type(t,e)},this)},getCollection:function(e){e instanceof n.RelationalModel&&(e=e.constructor);var r=e;while(r._superModel)r=r._superModel;var i=t.detect(this._collections,function(e){return e.model===r});return i||(i=this._createCollection(e)),i},getObjectByName:function(e){var n=e.split("."),r=null;return t.find(this._modelScopes,function(e){r=t.reduce(n,function(e,t){return e[t]},e);if(r&&r!==e)return!0},this),r},_createCollection:function(e){var t;return e instanceof n.RelationalModel&&(e=e.constructor),e.prototype instanceof n.RelationalModel&&(t=new n.Collection,t.model=e,this._collections.push(t)),t},resolveIdForItem:function(e,r){var i=t.isString(r)||t.isNumber(r)?r:null;return i==null&&(r instanceof n.RelationalModel?i=r.id:t.isObject(r)&&(i=r[e.prototype.idAttribute])),i},find:function(e,t){var n=this.resolveIdForItem(e,t),r=this.getCollection(e);if(r){var i=r.get(n);if(i instanceof e)return i}return null},register:function(e){var t=e.collection,n=this.getCollection(e);n&&n.add(e),e.bind("destroy",this.unregister,this),e.collection=t},update:function(e){var t=this.getCollection(e);t._onModelEvent("change:"+e.idAttribute,e,t)},unregister:function(e){e.unbind("destroy",this.unregister);var t=this.getCollection(e);t&&t.remove(e)}}),n.Relational.store=new n.Store,n.Relation=function(e,r){this.instance=e,r=t.isObject(r)?r:{},this.reverseRelation=t.defaults(r.reverseRelation||{},this.options.reverseRelation),this.reverseRelation.type=t.isString(this.reverseRelation.type)?n[this.reverseRelation.type]||n.Relational.store.getObjectByName(this.reverseRelation.type):this.reverseRelation.type,this.model=r.model||this.instance.constructor,this.options=t.defaults(r,this.options,n.Relation.prototype.options),this.key=this.options.key,this.keySource=this.options.keySource||this.key,this.keyDestination=this.options.keyDestination||this.keySource||this.key,this.relatedModel=this.options.relatedModel,t.isString(this.relatedModel)&&(this.relatedModel=n.Relational.store.getObjectByName(this.relatedModel));if(!this.checkPreconditions())return!1;e&&(this.keyContents=this.instance.get(this.keySource),this.key!==this.keySource&&this.instance.unset(this.keySource,{silent:!0}),this.instance._relations.push(this)),!this.options.isAutoRelation&&this.reverseRelation.type&&this.reverseRelation.key&&n.Relational.store.addReverseRelation(t.defaults({isAutoRelation:!0,model:this.relatedModel,relatedModel:this.model,reverseRelation:this.options},this.reverseRelation)),t.bindAll(this,"_modelRemovedFromCollection","_relatedModelAdded","_relatedModelRemoved"),e&&(this.initialize(),n.Relational.store.getCollection(this.instance).bind("relational:remove",this._modelRemovedFromCollection),n.Relational.store.getCollection(this.relatedModel).bind("relational:add",this._relatedModelAdded).bind("relational:remove",this._relatedModelRemoved))},n.Relation.extend=n.Model.extend,t.extend(n.Relation.prototype,n.Events,n.Semaphore,{options:{createModels:!0,includeInJSON:!0,isAutoRelation:!1},instance:null,key:null,keyContents:null,relatedModel:null,reverseRelation:null,related:null,_relatedModelAdded:function(e,t,n){var r=this;e.queue(function(){r.tryAddRelated(e,n)})},_relatedModelRemoved:function(e,t,n){this.removeRelated(e,n)},_modelRemovedFromCollection:function(e){e===this.instance&&this.destroy()},checkPreconditions:function(){var e=this.instance,r=this.key,i=this.model,s=this.relatedModel,o=n.Relational.showWarnings&&typeof console!="undefined";if(!i||!r||!s)return o&&console.warn("Relation=%o; no model, key or relatedModel (%o, %o, %o)",this,i,r,s),!1;if(i.prototype instanceof n.RelationalModel){if(s.prototype instanceof n.RelationalModel){if(this instanceof n.HasMany&&this.reverseRelation.type===n.HasMany)return o&&console.warn("Relation=%o; relation is a HasMany, and the reverseRelation is HasMany as well.",this),!1;if(e&&e._relations.length){var u=t.any(e._relations,function(e){var t=this.reverseRelation.key&&e.reverseRelation.key;return e.relatedModel===s&&e.key===r&&(!t||this.reverseRelation.key===e.reverseRelation.key)},this);if(u)return o&&console.warn("Relation=%o between instance=%o.%s and relatedModel=%o.%s already exists",this,e,r,s,this.reverseRelation.key),!1}return!0}return o&&console.warn("Relation=%o; relatedModel does not inherit from Backbone.RelationalModel (%o)",this,s),!1}return o&&console.warn("Relation=%o; model does not inherit from Backbone.RelationalModel (%o)",this,e),!1},setRelated:function(e,n){this.related=e,this.instance.acquire(),this.instance.set(this.key,e,t.defaults(n||{},{silent:!0})),this.instance.release()},_isReverseRelation:function(e){return e.instance instanceof this.relatedModel&&this.reverseRelation.key===e.key&&this.key===e.reverseRelation.key?!0:!1},getReverseRelations:function(e){var n=[],r=t.isUndefined(e)?this.related&&(this.related.models||[this.related]):[e];return t.each(r,function(e){t.each(e.getRelations(),function(e){this._isReverseRelation(e)&&n.push(e)},this)},this),n},sanitizeOptions:function(e){return e=e?t.clone(e):{},e.silent&&(e.silentChange=!0,delete e.silent),e},unsanitizeOptions:function(e){return e=e?t.clone(e):{},e.silentChange&&(e.silent=!0,delete e.silentChange),e},destroy:function(){n.Relational.store.getCollection(this.instance).unbind("relational:remove",this._modelRemovedFromCollection),n.Relational.store.getCollection(this.relatedModel).unbind("relational:add",this._relatedModelAdded).unbind("relational:remove",this._relatedModelRemoved),t.each(this.getReverseRelations(),function(e){e.removeRelated(this.instance)},this)}}),n.HasOne=n.Relation.extend({options:{reverseRelation:{type:"HasMany"}},initialize:function(){t.bindAll(this,"onChange"),this.instance.bind("relational:change:"+this.key,this.onChange);var e=this.findRelated({silent:!0});this.setRelated(e),t.each(this.getReverseRelations(),function(e){e.addRelated(this.instance)},this)},findRelated:function(e){var t=this.keyContents,n=null;return t instanceof this.relatedModel?n=t:t&&(n=this.relatedModel.findOrCreate(t,{create:this.options.createModels})),n},onChange:function(e,r,i){if(this.isLocked())return;this.acquire(),i=this.sanitizeOptions(i);var s=t.isUndefined(i._related),o=s?this.related:i._related;if(s){this.keyContents=r;if(r instanceof this.relatedModel)this.related=r;else if(r){var u=this.findRelated(i);this.setRelated(u)}else this.setRelated(null)}o&&this.related!==o&&t.each(this.getReverseRelations(o),function(e){e.removeRelated(this.instance,i)},this),t.each(this.getReverseRelations(),function(e){e.addRelated(this.instance,i)},this);if(!i.silentChange&&this.related!==o){var a=this;n.Relational.eventQueue.add(function(){a.instance.trigger("update:"+a.key,a.instance,a.related,i)})}this.release()},tryAddRelated:function(e,t){if(this.related)return;t=this.sanitizeOptions(t);var r=this.keyContents;if(r){var i=n.Relational.store.resolveIdForItem(this.relatedModel,r);e.id===i&&this.addRelated(e,t)}},addRelated:function(e,t){if(e!==this.related){var n=this.related||null;this.setRelated(e),this.onChange(this.instance,e,{_related:n})}},removeRelated:function(e,t){if(!this.related)return;if(e===this.related){var n=this.related||null;this.setRelated(null),this.onChange(this.instance,e,{_related:n})}}}),n.HasMany=n.Relation.extend({collectionType:null,options:{reverseRelation:{type:"HasOne"},collectionType:n.Collection,collectionKey:!0,collectionOptions:{}},initialize:function(){t.bindAll(this,"onChange","handleAddition","handleRemoval","handleReset"),this.instance.bind("relational:change:"+this.key,this.onChange),this.collectionType=this.options.collectionType,t.isString(this.collectionType)&&(this.collectionType=n.Relational.store.getObjectByName(this.collectionType));if(!this.collectionType.prototype instanceof n.Collection)throw new Error("collectionType must inherit from Backbone.Collection");this.keyContents instanceof n.Collection?this.setRelated(this._prepareCollection(this.keyContents)):this.setRelated(this._prepareCollection()),this.findRelated({silent:!0})},_getCollectionOptions:function(){return t.isFunction(this.options.collectionOptions)?this.options.collectionOptions(this.instance):this.options.collectionOptions},_prepareCollection:function(e){this.related&&this.related.unbind("relational:add",this.handleAddition).unbind("relational:remove",this.handleRemoval).unbind("relational:reset",this.handleReset);if(!e||!(e instanceof n.Collection))e=new this.collectionType([],this._getCollectionOptions());e.model=this.relatedModel;if(this.options.collectionKey){var t=this.options.collectionKey===!0?this.options.reverseRelation.key:this.options.collectionKey;e[t]&&e[t]!==this.instance?n.Relational.showWarnings&&typeof console!="undefined"&&console.warn("Relation=%o; collectionKey=%s already exists on collection=%o",this,t,this.options.collectionKey):t&&(e[t]=this.instance)}return e.bind("relational:add",this.handleAddition).bind("relational:remove",this.handleRemoval).bind("relational:reset",this.handleReset),e},findRelated:function(e){if(this.keyContents){var r=[];this.keyContents instanceof n.Collection?r=this.keyContents.models:(this.keyContents=t.isArray(this.keyContents)?this.keyContents:[this.keyContents],t.each(this.keyContents,function(e){var t=null;e instanceof this.relatedModel?t=e:t=this.relatedModel.findOrCreate(e,{create:this.options.createModels}),t&&!this.related.getByCid(t)&&!this.related.get(t)&&r.push(t)},this)),r.length&&(e=this.unsanitizeOptions(e),this.related.add(r,e))}},onChange:function(e,r,i){i=this.sanitizeOptions(i),this.keyContents=r,t.each(this.getReverseRelations(),function(e){e.removeRelated(this.instance,i)},this);if(r instanceof n.Collection)this._prepareCollection(r),this.related=r;else{var s;this.related instanceof n.Collection?(s=this.related,s.remove(s.models)):s=this._prepareCollection(),this.setRelated(s),this.findRelated(i)}t.each(this.getReverseRelations(),function(e){e.addRelated(this.instance,i)},this);var o=this;n.Relational.eventQueue.add(function(){!i.silentChange&&o.instance.trigger("update:"+o.key,o.instance,o.related,i)})},tryAddRelated:function(e,r){r=this.sanitizeOptions(r);if(!this.related.getByCid(e)&&!this.related.get(e)){var i=t.any(this.keyContents,function(t){var r=n.Relational.store.resolveIdForItem(this.relatedModel,t);return r&&r===e.id},this);i&&this.related.add(e,r)}},handleAddition:function(e,r,i){if(!(e instanceof n.Model))return;i=this.sanitizeOptions(i),t.each(this.getReverseRelations(e),function(e){e.addRelated(this.instance,i)},this);var s=this;n.Relational.eventQueue.add(function(){!i.silentChange&&s.instance.trigger("add:"+s.key,e,s.related,i)})},handleRemoval:function(e,r,i){if(!(e instanceof n.Model))return;i=this.sanitizeOptions(i),t.each(this.getReverseRelations(e),function(e){e.removeRelated(this.instance,i)},this);var s=this;n.Relational.eventQueue.add(function(){!i.silentChange&&s.instance.trigger("remove:"+s.key,e,s.related,i)})},handleReset:function(e,t){t=this.sanitizeOptions(t);var r=this;n.Relational.eventQueue.add(function(){!t.silentChange&&r.instance.trigger("reset:"+r.key,r.related,t)})},addRelated:function(e,t){var n=this;t=this.unsanitizeOptions(t),e.queue(function(){n.related&&!n.related.getByCid(e)&&!n.related.get(e)&&n.related.add(e,t)})},removeRelated:function(e,t){t=this.unsanitizeOptions(t),(this.related.getByCid(e)||this.related.get(e))&&this.related.remove(e,t)}}),n.RelationalModel=n.Model.extend({relations:null,_relations:null,_isInitialized:!1,_deferProcessing:!1,_queue:null,subModelTypeAttribute:"type",subModelTypes:null,constructor:function(e,r){var i=this;if(r&&r.collection){this._deferProcessing=!0;var s=function(e){e===i&&(i._deferProcessing=!1,i.processQueue(),r.collection.unbind("relational:add",s))};r.collection.bind("relational:add",s),t.defer(function(){s(i)})}this._queue=new n.BlockingQueue,this._queue.block(),n.Relational.eventQueue.block(),n.Model.apply(this,arguments),n.Relational.eventQueue.unblock()},trigger:function(e){if(e.length>5&&"change"===e.substr(0,6)){var t=this,r=arguments;n.Relational.eventQueue.add(function(){n.Model.prototype.trigger.apply(t,r)})}else n.Model.prototype.trigger.apply(this,arguments);return this},initializeRelations:function(){this.acquire(),this._relations=[],t.each(this.relations,function(e){var r=t.isString(e.type)?n[e.type]||n.Relational.store.getObjectByName(e.type):e.type;r&&r.prototype instanceof n.Relation?new r(this,e):n.Relational.showWarnings&&typeof console!="undefined"&&console.warn("Relation=%o; missing or invalid type!",e)},this),this._isInitialized=!0,this.release(),this.processQueue()},updateRelations:function(e){this._isInitialized&&!this.isLocked()&&t.each(this._relations,function(t){var n=this.attributes[t.keySource]||this.attributes[t.key];t.related!==n&&this.trigger("relational:change:"+t.key,this,n,e||{})},this)},queue:function(e){this._queue.add(e)},processQueue:function(){this._isInitialized&&!this._deferProcessing&&this._queue.isBlocked()&&this._queue.unblock()},getRelation:function(e){return t.detect(this._relations,function(t){if(t.key===e)return!0},this)},getRelations:function(){return this._relations},fetchRelated:function(e,r,i){r||(r={});var s,o=[],u=this.getRelation(e),a=u&&u.keyContents,f=a&&t.select(t.isArray(a)?a:[a],function(e){var t=n.Relational.store.resolveIdForItem(u.relatedModel,e);return t&&(i||!n.Relational.store.find(u.relatedModel,t))},this);if(f&&f.length){var l=t.map(f,function(e){var n;if(t.isObject(e))n=u.relatedModel.build(e);else{var r={};r[u.relatedModel.prototype.idAttribute]=e,n=u.relatedModel.build(r)}return n},this);u.related instanceof n.Collection&&t.isFunction(u.related.url)&&(s=u.related.url(l));if(s&&s!==u.related.url()){var c=t.defaults({error:function(){var e=arguments;t.each(l,function(t){t.trigger("destroy",t,t.collection,r),r.error&&r.error.apply(t,e)})},url:s},r,{add:!0});o=[u.related.fetch(c)]}else o=t.map(l,function(e){var n=t.defaults({error:function(){e.trigger("destroy",e,e.collection,r),r.error&&r.error.apply(e,arguments)}},r);return e.fetch(n)},this)}return o},set:function(e,r,i){n.Relational.eventQueue.block();var s;t.isObject(e)||e==null?(s=e,i=r):(s={},s[e]=r);var o=n.Model.prototype.set.apply(this,arguments);return!this._isInitialized&&!this.isLocked()?(this.constructor.initializeModelHierarchy(),n.Relational.store.register(this),this.initializeRelations()):s&&this.idAttribute in s&&n.Relational.store.update(this),s&&this.updateRelations(i),n.Relational.eventQueue.unblock(),o},unset:function(e,t){n.Relational.eventQueue.block();var r=n.Model.prototype.unset.apply(this,arguments);return this.updateRelations(t),n.Relational.eventQueue.unblock(),r},clear:function(e){n.Relational.eventQueue.block();var t=n.Model.prototype.clear.apply(this,arguments);return this.updateRelations(e),n.Relational.eventQueue.unblock(),t},change:function(e){var t=this,r=arguments;n.Relational.eventQueue.add(function(){n.Model.prototype.change.apply(t,r)})},clone:function(){var e=t.clone(this.attributes);return t.isUndefined(e[this.idAttribute])||(e[this.idAttribute]=null),t.each(this.getRelations(),function(t){delete e[t.key]}),new this.constructor(e)},toJSON:function(){if(this.isLocked())return this.id;this.acquire();var e=n.Model.prototype.toJSON.call(this);return this.constructor._superModel&&!(this.constructor._subModelTypeAttribute in e)&&(e[this.constructor._subModelTypeAttribute]=this.constructor._subModelTypeValue),t.each(this._relations,function(r){var i=e[r.key];if(r.options.includeInJSON===!0)i&&t.isFunction(i.toJSON)?e[r.keyDestination]=i.toJSON():e[r.keyDestination]=null;else if(t.isString(r.options.includeInJSON))i instanceof n.Collection?e[r.keyDestination]=i.pluck(r.options.includeInJSON):i instanceof n.Model?e[r.keyDestination]=i.get(r.options.includeInJSON):e[r.keyDestination]=null;else if(t.isArray(r.options.includeInJSON))if(i instanceof n.Collection){var s=[];i.each(function(e){var n={};t.each(r.options.includeInJSON,function(t){n[t]=e.get(t)}),s.push(n)}),e[r.keyDestination]=s}else if(i instanceof n.Model){var s={};t.each(r.options.includeInJSON,function(e){s[e]=i.get(e)}),e[r.keyDestination]=s}else e[r.keyDestination]=null;else delete e[r.key];r.keyDestination!==r.key&&delete e[r.key]}),this.release(),e}},{setup:function(e){this.prototype.relations=(this.prototype.relations||[]).slice(0),this._subModels={},this._superModel=null,this.prototype.hasOwnProperty("subModelTypes")?n.Relational.store.addSubModels(this.prototype.subModelTypes,this):this.prototype.subModelTypes=null,t.each(this.prototype.relations,function(e){e.model||(e.model=this);if(e.reverseRelation&&e.model===this){var r=!0;if(t.isString(e.relatedModel)){var i=n.Relational.store.getObjectByName(e.relatedModel);r=i&&i.prototype instanceof n.RelationalModel}var s=t.isString(e.type)?n[e.type]||n.Relational.store.getObjectByName(e.type):e.type;r&&s&&s.prototype instanceof n.Relation&&new s(null,e)}},this)},build:function(e,t){var n=this;this.initializeModelHierarchy();if(this._subModels&&this.prototype.subModelTypeAttribute in e){var r=e[this.prototype.subModelTypeAttribute],i=this._subModels[r];i&&(n=i)}return new n(e,t)},initializeModelHierarchy:function(){if(t.isUndefined(this._superModel)||t.isNull(this._superModel)){n.Relational.store.setupSuperModel(this);if(this._superModel){if(this._superModel.prototype.relations){var e=t.any(this.prototype.relations,function(e){return e.model&&e.model!==this},this);e||(this.prototype.relations=this._superModel.prototype.relations.concat(this.prototype.relations))}}else this._superModel=!1}this.prototype.subModelTypes&&t.keys(this.prototype.subModelTypes).length!==t.keys(this._subModels).length&&t.each(this.prototype.subModelTypes,function(e){var t=n.Relational.store.getObjectByName(e);t&&t.initializeModelHierarchy()})},findOrCreate:function(e,r){var i=n.Relational.store.find(this,e);if(t.isObject(e))if(i)i.set(e,r);else if(!r||r&&r.create!==!1)i=this.build(e,r);return i}}),t.extend(n.RelationalModel.prototype,n.Semaphore),n.Collection.prototype.__prepareModel=n.Collection.prototype._prepareModel,n.Collection.prototype._prepareModel=function(e,t){t||(t={});if(e instanceof n.Model)e.collection||(e.collection=this);else{var r=e;t.collection=this,typeof this.model.build!="undefined"?e=this.model.build(r,t):e=new this.model(r,t),e._validate(e.attributes,t)||(e=!1)}return e};var i=n.Collection.prototype.__add=n.Collection.prototype.add;n.Collection.prototype.add=function(e,r){r||(r={}),t.isArray(e)||(e=[e]);var s=[];return t.each(e,function(e){if(!(e instanceof n.Model)){var t=n.Relational.store.find(this.model,e[this.model.prototype.idAttribute]);t?(t.set(t.parse?t.parse(e):e,r),e=t):e=n.Collection.prototype._prepareModel.call(this,e,r)}e instanceof n.Model&&!this.get(e)&&!this.getByCid(e)&&s.push(e)},this),s.length&&(i.call(this,s,r),t.each(s,function(e){this.trigger("relational:add",e,this,r)},this)),this};var s=n.Collection.prototype.__remove=n.Collection.prototype.remove;n.Collection.prototype.remove=function(e,r){return r||(r={}),t.isArray(e)?e=e.slice(0):e=[e],t.each(e,function(e){e=this.getByCid(e)||this.get(e),e instanceof n.Model&&(s.call(this,e,r),this.trigger("relational:remove",e,this,r))},this),this};var o=n.Collection.prototype.__reset=n.Collection.prototype.reset;n.Collection.prototype.reset=function(e,t){return o.call(this,e,t),this.trigger("relational:reset",this,t),this};var u=n.Collection.prototype.__sort=n.Collection.prototype.sort;n.Collection.prototype.sort=function(e){return u.call(this,e),this.trigger("relational:reset",this,e),this};var a=n.Collection.prototype.__trigger=n.Collection.prototype.trigger;n.Collection.prototype.trigger=function(e){if(e==="add"||e==="remove"||e==="reset"){var t=this,r=arguments;n.Relational.eventQueue.add(function(){a.apply(t,r)})}else a.apply(this,arguments);return this},n.RelationalModel.extend=function(e,t){var r=n.Model.extend.apply(this,arguments);return r.setup(this),r}})();