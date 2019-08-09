<?php
declare( strict_types = 1 );

namespace App\Http\Controllers\Api\Pm;

use Illuminate\Http\{
    JsonResponse,
    Response    
};
use Auth;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Pm\MessageRepositoryInterface;
use App\Services\Fractal\FractalManager;
use App\Transformers\Pm\MessageTransformer;
use App\Models\Pm\{
    Dialog,
    Recipient
};
use App\Http\Requests\Pm\Message\{
    IndexRequest,
    SendRequest,
    DeleteRequest
};

/**
 * Class    MessageController
 * @package App\Http\Controllers\Api\Pm
 * @group   pm.dialog.messages
 */
class MessageController extends Controller
{
    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
     * PollableController constructor.
     * @param DialogRepositoryInterfa $messageRepository
     */
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * Get message of dialog
     *
     * @authenticated
     *     
     * @responseFile 200 responses/pm/messages/index_200.json
     * @responseFile 401 responses/pm/messages/index_401.json
     * @responseFile 404 responses/pm/messages/index_404.json
     * 
     * @param Dialog        $dialog
     * @param IndexRequest  $request
     * 
     * @return JsonResponse
     */
    public function index(Dialog $dialog, IndexRequest $request) : JsonResponse
    {
        return response()->json(FractalManager::collectionToFractalPaginate(
            $request,
            $this->messageRepository->messagesDialog($dialog, $request->user(), $request->validated()),
            new MessageTransformer
        ));
    }
            
    /**
     * Send message to dialog
     *
     * @authenticated
     *
     * @responseFile 200 responses/pm/messages/send_200.json
     * @responseFile 422 responses/pm/messages/send_422.json
     * @responseFile 401 responses/pm/messages/send_401.json
     * @responseFile 404 responses/pm/messages/send_404.json
     * 
     * @param Dialog        $dialog
     * @param ShowRequest   $request
     * 
     * @return JsonResponse
     */
    public function send(Dialog $dialog, SendRequest $request) : JsonResponse
    {
        $message = $this->messageRepository->send($dialog, $request->user(), $request->validated());
        
        return response()->json(FractalManager::formatResourceFractal(
            fractal()
            ->item($message, new MessageTransformer)
        ));
    }

    /**
     * Delete message from dialog
     *
     * @authenticated
     * 
     * @responseFile 201 responses/pm/messages/delete_201.json
     * @responseFile 404 responses/pm/messages/delete_404.json
     *
     * @param Dialog        $dialog
     * @param Message       $message
     * @param ShowRequest   $request
     * 
     * @return JsonResponse
     */
    public function delete(Dialog $dialog, Recipient $recipient, DeleteRequest $request) : JsonResponse
    {
        $this->messageRepository->delete($recipient, $request->user());
        
        return response()->json(['data' => '']);
    }
}
