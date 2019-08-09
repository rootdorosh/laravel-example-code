<?php
namespace Tests\Feature\Http\Controllers;

use Faker\Factory as Faker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Role;
use App\Models\Pm\Dialog;

/**
 * Class DialogControllerTest
 * @package Tests\Feature\Http\Controllers
 * @group   pm.dialog
 */
class DialogControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    
    /**
     * @var array
     */
    private static $headers;

    /**
     * @var User
     */
    private $user;

    /**
     * @var DialogRepositoryInterface
     */
    private $dialogRepository;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    public function setUp() : void
    {
        parent::setUp();
            
        $this->dialogRepository = $this->app->make('App\Repositories\Interfaces\Pm\DialogRepositoryInterface');
        $this->messageRepository = $this->app->make('App\Repositories\Interfaces\Pm\MessageRepositoryInterface');
        
        $this->user = factory(User::class)->create();
        $this->user->attachRole(Role::admin()->first());
        
        $token = $this->user->createToken('ApiToken')->accessToken;
        
        self::$headers = [
            'Authorization'    => 'Bearer ' . $token,
        ];
    }
    
    /**
     * @test
     */
    public function index()
    {
        $user = $this->user;
        $interlocutor = factory(User::class)->create();
        $interlocutor2 = factory(User::class)->create();
        
        $dialog = $this->dialogRepository->make($user, [$interlocutor->id, $interlocutor2->id]);
        $this->messageRepository->send($dialog, $interlocutor, ['text' => 'Hello 01']);
        $this->messageRepository->send($dialog, $interlocutor2, ['text' => 'Hello 02']);
        
        $dialog1 = $this->dialogRepository->make($user, [$interlocutor->id]);
        $this->messageRepository->send($dialog1, $user, ['text' => 'Hello 11']);
        $this->messageRepository->send($dialog1, $user, ['text' => 'Hello 12']);

        $dialog2 = $this->dialogRepository->make($user, [$interlocutor2->id]);
        $this->messageRepository->send($dialog2, $user, ['text' => 'Hello 21']);
        $this->messageRepository->send($dialog2, $user, ['text' => 'Hello 22']);

        $url = '/api/pm';
    
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);
        
        $this->saveResponse($response, 'pm/dialogs/index_200.json');        
    }
    
    /**
     * @test
     */
    public function id()
    {
        $user = $this->user;
        $interlocutor = factory(User::class)->create();
        $interlocutor2 = factory(User::class)->create();

        $url = '/api/pm/id';
        $data = ['users' => [$interlocutor->id, $interlocutor2->id]];
    
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(200);
        
        $this->saveResponse($response, 'pm/dialogs/id_200.json');   
        
        $interlocutor2->delete();
        
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(422);
        
        $this->saveResponse($response, 'pm/dialogs/id_422.json');   
    }

    /**
     * @test
     */
    public function show()
    {
        $user = $this->user;
        $interlocutor = factory(User::class)->create();
        $interlocutor2 = factory(User::class)->create();
        
        $dialog = $this->dialogRepository->make($user, [$interlocutor->id, $interlocutor2->id]);

        $url = '/api/pm/' . $dialog->id;
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'pm/dialogs/show_200.json');   
        
        $dialog = $this->dialogRepository->make($interlocutor, [$interlocutor2->id]);
        $url = '/api/pm/' . $dialog->id;
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(401);        
        $this->saveResponse($response, 'pm/dialogs/show_401.json');   
 
        $url = '/api/pm/0';
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'pm/dialogs/show_404.json');   
    }

    /**
     * @test
     */
    public function remove()
    {
        $user = $this->user;
        $interlocutor = factory(User::class)->create();
        $interlocutor2 = factory(User::class)->create();
        
        $dialog = $this->dialogRepository->make($user, [$interlocutor->id, $interlocutor2->id]);
        $this->messageRepository->send($dialog, $user, ['text' => 'Hello 00']);
        $this->messageRepository->send($dialog, $interlocutor, ['text' => 'Hello 01']);
        $this->messageRepository->send($dialog, $interlocutor2, ['text' => 'Hello 02']);

        
        $url = '/api/pm/' . $dialog->id;
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(200);        
        $this->saveResponse($response, 'pm/dialogs/delete_200.json');   
        
        $dialog = $this->dialogRepository->make($interlocutor, [$interlocutor2->id]);
        $url = '/api/pm/' . $dialog->id;
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(401);        
        $this->saveResponse($response, 'pm/dialogs/delete_401.json');   
 
        $url = '/api/pm/0';
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);        
        $this->saveResponse($response, 'pm/dialogs/delete_404.json');   
    }
}
