<?php
namespace Features\Users;

use App\Users\User;
use Tests\BasicTestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserFeatureTest extends BasicTestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @test */
    public function it_should_error_on_delete_user_when_id_is_not_found()
    {
        $user = factory(User::class,10)->create();
        $this->delete('/users/9999', []);
        $this->seeStatusCode(404);
        $this->assertEquals('{"message":"Not Found"}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_update_user_when_id_is_not_found()
    {
        $user = factory(User::class,10)->create();
        $data = [
            'name'=>'asdasdasdasd',
            'email'=>'test@test.com'
        ];
        $this->put('/users/9999', $data, []);
        $this->seeStatusCode(404);
        $this->assertEquals('{"message":"Not Found"}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_update_user_when_email_invalid_format()
    {
        $user = factory(User::class,10)->create();
        $data = [
            'name'=>'asdasdasdasd',
            'email'=>'test'
        ];
        $this->put('/users/'.$user[2]->id, $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"email":["The email must be a valid email address."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_update_user_when_email_already_taken()
    {
        $user = factory(User::class,10)->create();
        $data = [
            'name'=>'asdasdasdasd',
            'email'=>$user[3]->email
        ];
        $this->put('/users/'.$user[2]->id, $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"email":["The email has already been taken."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_update_user_when_name_is_not_in_minimum_limit()
    {
        $user = factory(User::class)->create();
        $data = [
            'name'=>'a',
            'email'=>'test@test.com'
        ];
        $this->put('/users/'.$user->id, $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"name":["The name must be at least 2 characters."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_update_user_when_name_is_exceed_to_maximum_limit()
    {
        $user = factory(User::class)->create();
        $data = [
            'name'=>$this->faker->sentence(30, true),
            'email'=>'test@test.com'
        ];
        $this->put('/users/'.$user->id, $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"name":["The name may not be greater than 50 characters."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_update_user_when_name_is_integer()
    {
        $user = factory(User::class)->create();
        $data = [
            'name'=>123456687,
            'email'=>'test@test.com'
        ];
        $this->put('/users/'.$user->id, $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"name":["The name must be a string."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_update_user_when_fields_is_empty()
    {
        $user = factory(User::class)->create();
        $data = [
            'name'=>'',
            'email'=>''
        ];
        $this->put('/users/'.$user->id, $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"name":["The name field is required."],"email":["The email field is required."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_return_empty_on_list_all_users_when_users_table_is_empty()
    {
        $this->get('/users', []);
        $this->seeStatusCode(200);
        $this->assertEquals('[]',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_user_by_id_when_user_is_not_found()
    {
        $users = factory(User::class, 10)->create();
        $this->get('/users/9999', []);
        $this->seeStatusCode(404);
        $this->assertEquals('{"message":"Not Found"}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_name_is_integer()
    {
        $user = factory(User::class)->create();
        $data=[
            'name'=>1234567,
            'email'=>'test@test.com',
            'password'=>'asdasd',
            'c_password'=>'asdasd',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"name":["The name must be a string."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_email_is_invalid_format()
    {
        $user = factory(User::class)->create();
        $data=[
            'name'=>'adasdasd',
            'email'=>'test',
            'password'=>'asdasd',
            'c_password'=>'asdasd',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"email":["The email must be a valid email address."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_fields_is_empty()
    {
        $user = factory(User::class)->create();
        $data=[
            'name'=>'',
            'email'=>'',
            'password'=>'',
            'c_password'=>'',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"name":["The name field is required."],"email":["The email field is required."],"password":["The password field is required."],"c_password":["The c password field is required."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_password_is_not_in_minimum_limit()
    {
        $user = factory(User::class)->create();
        $data=[
            'name'=>'test',
            'email'=>'test@test.com',
            'password'=>'asd',
            'c_password'=>'asd',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"password":["The password must be at least 6 characters."],"c_password":["The c password must be at least 6 characters."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_password_has_special_character()
    {
        $user = factory(User::class)->create();
        $data=[
            'name'=>'test',
            'email'=>'test@test.com',
            'password'=>'asdasdasdasdasdasdsadasdasdsad!!!!',
            'c_password'=>'asdasdasdasdasdasdsadasdasdsad!!!!',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"password":["The password format is invalid."],"c_password":["The c password format is invalid."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_password_is_not_match_in_confirm_password()
    {
        $user = factory(User::class)->create();
        $data=[
            'name'=>'test',
            'email'=>'test@test.com',
            'password'=>'asdasdasdasdasdasd',
            'c_password'=>'asdasdasdasdasdasdsadasdasdsad',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"c_password":["The c password and password must match."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_email_is_taken()
    {
        $user = factory(User::class)->create();
        $data=[
            'name'=>'test',
            'email'=>$user->email,
            'password'=>'asdasdasdasdasdasdsadasdasdsad',
            'c_password'=>'asdasdasdasdasdasdsadasdasdsad',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"email":["The email has already been taken."]}',$this->response->getContent());
    }

    /** @test */
    public function it_should_error_on_create_user_when_name_is_exceed_maximum_limit()
    {
        $data=[
            'name'=>$this->faker->sentence(30, true),
            'email'=>'test@test.com',
            'password'=>'asdasdasdasdasdasdsadasdasdsad',
            'c_password'=>'asdasdasdasdasdasdsadasdasdsad',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(422);
        $this->assertEquals('{"name":["The name may not be greater than 50 characters."]}',$this->response->getContent());
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = factory(User::class)->create();
        $this->delete('/users/'.$user->id, [], []);
        $this->seeStatusCode(200);

        $this->assertEquals(1, $this->response->getContent());
        $this->notSeeInDatabase('users',[
            'name'=>$user->name,
            'email'=>$user->email
        ]);
    }

    /** @test */
    public function it_can_update_user()
    {
        $user = factory(User::class, 3)->create();
        $data=[
            'name'=>'test',
            'email'=>'test@test.com',
        ];
        $this->put('/users/'.$user[2]->id, $data, []);
        $this->seeStatusCode(200);

        $this->assertEquals(1, $this->response->getContent());
    }

    /** @test */
    public function it_can_show_all_users()
    {
        $users = factory(User::class, 10)->create();
        $users2 = factory(User::class)->create();
        $this->get('/users', []);
        $this->seeStatusCode(200);

        $this->assertEquals(count(json_decode($this->response->getContent())), 11);
        $usersFound = json_decode($this->response->getContent());
        $this->assertEquals($usersFound[5]->name, $users[5]->name);
        $this->assertEquals($usersFound[5]->email, $users[5]->email);
    }

    /** @test */
    public function it_can_show_user_by_id()
    {
        $users = factory(User::class, 10)->create();
        $this->get('/users/'.$users[3]->id, []);
        $this->seeStatusCode(200);

        $user = json_decode($this->response->getContent());
        $this->assertEquals($user->name, $users[3]->name);
        $this->assertEquals($user->email, $users[3]->email);
    }

    /** @test */
    public function it_can_create_user()
    {
        $data=[
            'name'=>'test',
            'email'=>'test@test.com',
            'password'=>'asdasdasdasdasdasdsadasdasdsad',
            'c_password'=>'asdasdasdasdasdasdsadasdasdsad',
        ];
        $this->post('/users', $data, []);
        $this->seeStatusCode(200);
        $user = json_decode($this->response->getContent());
        $this->assertEquals('test', $user->name);
        $this->assertEquals('test@test.com', $user->email);
    }
}
