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
use App\Models\Pm\Message;

/**
 * Class MessageControllerTest
 * @package Tests\Feature\Http\Controllers
 * @group   pm.dialog.message
 */
class MessageControllerTest extends TestCase
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
        $this->messageRepository->send($dialog, $user, ['text' => 'Hello 00']);
        $this->messageRepository->send($dialog, $interlocutor, ['text' => 'Hello 01']);
        $this->messageRepository->send($dialog, $interlocutor2, ['text' => 'Hello 02']);
        
        $url = '/api/pm/' . $dialog->id . '/messages';    
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(200);
        $this->saveResponse($response, 'pm/messages/index_200.json');        
        
        $dialog = $this->dialogRepository->make($interlocutor, [$interlocutor2->id]);
        $url = '/api/pm/' . $dialog->id . '/messages';    
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(401);
        $this->saveResponse($response, 'pm/messages/index_401.json');        
        
        $dialog->delete();
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);
        $this->saveResponse($response, 'pm/messages/index_404.json');                
    }
    
    /**
     * @test
     */
    public function send()
    {
        $user = $this->user;
        $interlocutor = factory(User::class)->create();
        $interlocutor2 = factory(User::class)->create();
        
        $dialog = $this->dialogRepository->make($user, [$interlocutor->id, $interlocutor2->id]);
        
        $data = ['text' => 'Hello'];
        $url = '/api/pm/' . $dialog->id . '/messages';    
        $response = $this->json('POST', $url, $data, self::$headers);
        $response->assertStatus(200);
        $this->saveResponse($response, 'pm/messages/send_200.json');        
        
        
        $response = $this->json('POST', $url, [], self::$headers);
        $response->assertStatus(422);
        $this->saveResponse($response, 'pm/messages/send_422.json');        
        
        $dialog->delete();
        $response = $this->json('GET', $url, [], self::$headers);
        $response->assertStatus(404);
        $this->saveResponse($response, 'pm/messages/send_404.json');  
        
        $dialog = $this->dialogRepository->make($interlocutor, [$interlocutor2->id]);
        $url = '/api/pm/' . $dialog->id . '/messages';    
        $response = $this->json('GET', $url, $data, self::$headers);
        $response->assertStatus(401);
        $this->saveResponse($response, 'pm/messages/send_401.json');  
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
        
        $data = ['text' => 'Hello'];
        $url = '/api/pm/' . $dialog->id . '/messages';    
        $response = $this->json('POST', $url, $data, self::$headers);
        $data = $response->getData();
        
        $url = '/api/pm/' . $dialog->id . '/messages/' . $data->data->recipient_id; 
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(200);
        $this->saveResponse($response, 'pm/messages/delete_200.json');        
        
        $response = $this->json('DELETE', $url, [], self::$headers);
        $response->assertStatus(404);
        $this->saveResponse($response, 'pm/messages/delete_404.json');        
    }
    
}
