<?php
namespace Units\Users;

use App\Users\Exceptions\UserInvalidArgumentException;
use App\Users\Exceptions\UserNotFoundException;
use App\Users\Repositories\UserRepository;
use App\Users\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\BasicTestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserUnitTest extends BasicTestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @test */
    public function it_should_return_false_on_update_user_when_fields_is_empty(){
        $this->expectException(UserInvalidArgumentException::class);
        $user = factory(User::class, 2)->create();
        $userRepo = new UserRepository($user[0]);
        $updated =$userRepo->updateUser([
            'name'=>NULL,
            'email'=>NULL
        ]);
        $this->assertFalse($updated);
    }


    /** @test */
    public function it_should_return_false_on_update_user_when_user_is_not_found(){
        $userRepo = new UserRepository(new User());
        $updated =$userRepo->updateUser([]);
        $this->assertFalse($updated);
    }

    /** @test */
    public function it_should_error_on_update_user_when_email_is_taken(){
        $this->expectException(UserInvalidArgumentException::class);
        $user = factory(User::class, 2)->create();
        $userRepo = new UserRepository($user[0]);
        $updated =$userRepo->updateUser([
            'name'=>'',
            'email'=>$user[1]->email,
            'password'=>''
        ]);
    }

    /** @test */
    public function it_should_retun_empty_on_list_all_user_when_empty_users_table(){
        $userRepo = new UserRepository(new User());
        $found = $userRepo->listAllUsers();

        $this->assertInstanceOf(Collection::class, $found);
        $this->assertEquals(0, count($found));
    }

    /** @test */
    public function it_should_error_on_find_by_user_email_when_email_is_invalid(){
        $this->expectException(UserNotFoundException::class);
        $users = factory(User::class, 10)->create();
        $userRepo = new UserRepository(new User());
        $found = $userRepo->findUserByEmail('9999@9999.com');
    }

    /** @test */
    public function it_should_error_on_find_by_user_id_when_id_is_invalid(){
        $this->expectException(UserNotFoundException::class);
        $users = factory(User::class, 10)->create();
        $userRepo = new UserRepository(new User());
        $found = $userRepo->findUserById(9999);
    }

    /** @test */
    public  function it_should_error_on_create_user_when_(){
        $this->expectException(UserInvalidArgumentException::class);
        $userRepo = new UserRepository(new User());
        $user = $userRepo->createUser([]);
    }

    /** @test */
    public  function it_should_error_on_create_user_when_fields_is_null(){
        $this->expectException(UserInvalidArgumentException::class);
        $userRepo = new UserRepository(new User());
        $user = $userRepo->createUser([
            'name'=>NULL,
            'email'=>NULL,
            'password'=>NULL
        ]);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = factory(User::class)->create();
        $userRepo = new UserRepository($user);
        $updated = $userRepo->deleteUser();

        $this->assertTrue($updated);
        $this->notSeeInDatabase('users',[
            'name'=>$user->name,
            'email'=>$user->email
        ]);
    }

    /** @test */
    public function it_can_update_user()
    {
        $user = factory(User::class, 3)->create();
        $userRepo = new UserRepository($user[1]);
        $updated = $userRepo->updateUser([
            'name'=>'testname',
            'email'=>'testemail@test.com'
        ]);
        $found = $userRepo->findUserById($user[1]->id);

        $this->assertTrue($updated);
        $this->assertEquals($found->name, 'testname');
        $this->assertEquals($found->email, 'testemail@test.com');
    }

    /** @test */
    public function it_can_show_all_users()
    {
        $users = factory(User::class, 10)->create();
        $users2 = factory(User::class)->create();
        $userRepo = new UserRepository($users2);
        $found = $userRepo->listAllUsers();

        $this->assertInstanceOf(Collection::class, $found);
        $this->assertEquals(count($found), 11);
        $this->assertEquals($found[5]->name, $users[5]->name);
        $this->assertEquals($found[5]->email, $users[5]->email);
        $this->assertEquals($found[5]->password, $users[5]->password);
    }

    /** @test */
    public function it_can_show_user_by_email()
    {
        $users = factory(User::class, 10)->create();
        $userRepo = new UserRepository(new User());
        $found = $userRepo->findUserByEmail($users[5]->email);

        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals($found->name, $users[5]->name);
        $this->assertEquals($found->email, $users[5]->email);
        $this->assertEquals($found->password, $users[5]->password);
    }

    /** @test */
    public function it_can_show_user_by_id()
    {
        $users = factory(User::class, 10)->create();
        $userRepo = new UserRepository(new User());
        $found = $userRepo->findUserById($users[5]->id);

        $this->assertInstanceOf(User::class, $found);
        $this->assertEquals($found->name, $users[5]->name);
        $this->assertEquals($found->email, $users[5]->email);
        $this->assertEquals($found->password, $users[5]->password);
    }

    /** @test */
    public function it_can_create_user()
    {
        $userRepo = new UserRepository(new User());
        $user = $userRepo->createUser([
            'name'=>'test',
            'email'=>'test@test.com',
            'password'=>'asdasdasdasdasdasdsadasdasdsad'
        ]);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test', $user->name);
        $this->assertEquals('test@test.com', $user->email);
        $this->assertEquals('asdasdasdasdasdasdsadasdasdsad', $user->password);
    }
}
