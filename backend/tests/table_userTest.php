<?php
require_once 'table_user.php' ;


class table_userTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		user::drop_table() ;
	}

	protected function setUp()
	{
		user::clear_table() ;
	}

	protected function tearDown()
	{
		
	}

	public function test()
	{
		$username = "howdy" ;
		$password = "123456" ;
		$email    = "gg@gmail.com";

		user::create_new_user($username,$password,$email) ;
		$this->assertEquals(1,user::getNumberOfUsers(),"number of users is not correct after adding a new user") ;
		$r = user::getUserByUsername($username) ;
		$this->assertEquals($username,$r['username'],"username was not saved correctly") ;
		$this->assertEquals($email,$r['email'],"email is not saved correctly") ;
		$this->assertEquals(md5(md5($password).md5($r['salt'])),$r['password'],"password not correct") ;
		
		user::clear_table() ;
		$this->assertEquals(0,user::getNumberOfUsers()) ;

		$this->assertFalse(user::doesUsernameExist($username),"doesUsernameExist() in user is broken") ;
		user::create_new_user($username,$password,$email) ;
		$this->assertTrue(user::doesUsernameExist($username),"doesUsernameExist() in user is broken") ;

		$this->assertFalse(user::doesEmailExist($email."bla"),"doesEmailExist() in user is broken") ;
		$this->assertTrue(user::doesEmailExist($email),"doesEmailExist() in user is broken") ;

		$newPassword = "hello" ;
		user::resetPassword($username,$newPassword) ;
		$r = user::getUserByUsername($username) ;
		$this->assertEquals(md5(md5($newPassword).md5($r['salt'])),$r['password'],"restting password is broken") ;
		
		$newEmail = "ee@gmail.com" ;
		user::setEmail($username,$newEmail) ;
		$r = user::getUserByUsername($username) ;
		$this->assertEquals($newEmail,$r['email'],"setEmail is broken") ;


		user::deleteUserByUsername($username) ;
		$this->assertFalse(user::doesUsernameExist($username),"deleteUserByUsername() in user is broken") ;
		
		user::create_new_user($username,$password,$email) ;
		$this->assertTrue(user::isLogin($username,$password),"isLogin is broken") ;
		$this->assertFalse(user::isLogin($username,$password."d"),"isLogin is broken") ;
		$this->assertFalse(user::isLogin($username."d",$password),"isLogin is broken") ;
		
		$user_info = user::getUserByUsername($username) ;
		$user_info2 = user::getUserById($user_info['id']) ;
		$this->assertEquals($user_info['username'],$user_info2['username'],"mismatched usernames when getting user by ID") ;
		
		
		$gcm_id1 = "APA91bFpUo1z8PfiyCZG7HzThDyJ0MIg86BB1kj0A-ZGASK_iJ-RTu8pUB4t_5jMgwqkolWCahT4QOOAnp9nNdCox7pd9vlJao1-ncYHqvlS89lOpjdoci2_3XXGxcIWgrWwTz1tC8OlURokekQdbDCGKWuqfzfXLKrhisGxJYpF1ivuItZtJns" ;
		$this->assertTrue(safe_input::is_valid_gcm_id($gcm_id1),"[is_valid_gcm_id()]") ;
		
		//user::setGCM($user_id,$gcm_id)
		$this->assertTrue(user::setGCM($user_info['id'],$gcm_id1),"[user::setGCM()]") ;
		
		$user_info = user::getUserByUsername($username) ;
		$this->assertEquals($gcm_id1,$user_info['gcmID'],"setGCM()") ;
		
		$gcm_id2 = "APA91bHGJbxPpIUNirvnCQib7kojM12Qu2MBBd9dGHXSu0hsfB_Al2rQ4E8UWgpMXhNVIGT6IlSjLE-MB2F0RrBeN_llEYzPErIQoewxnDeON6uqBIHkLcMIY2NQtQHX3TNYBrlNc74wmh7aYec9kLMp5QGogVYSao1Q-RtIx4QV140YHBBASXM" ;
		$this->assertTrue(user::setGCM($user_info2['id'],$gcm_id2),"[user::setGCM()]") ;
		$user_info2 = user::getUserById($user_info['id']) ;
		$this->assertEquals($gcm_id2,$user_info2['gcmID'],"setGCM()") ;
		
	}

}

?>
