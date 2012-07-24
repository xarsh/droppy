define([
  'bootstrap',
  'model/user'
], function(bootstrap, User) {
	var appUser = new User;
	if(bootstrap.bootstrappedUser) {
		appUser.setFromJSON(bootstrap.bootstrappedUser);
	}
  
  return appUser;
});