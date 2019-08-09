<?php
declare( strict_types = 1 );

namespace App\Http\Controllers\Api\Pm;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Auth;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Pm\DialogRepositoryInterface;
use App\Services\Fractal\FractalManager;
use App\Models\Pm\Dialog;
use App\Transformers\Pm\{
    DialogListTransformer,
    DialogTransformer
};
use App\Http\Requests\Pm\Dialog\{
    IndexRequest,
    IdRequest,
    ShowRequest,
    DeleteRequest
};

/**
 * Class    DialogController
 * @package App\Http\Controllers\Api\Pm
 * @group   pm.dialog
 */
class DialogController extends Controller
{
    /**
     * @var DialogRepositoryInterface
     */
    private $dialogRepository;

    /**
     * PollableController constructor.
     * @param DialogRepositoryInterfa $dialogRepository
     */
    public function __construct(DialogRepositoryInterface $dialogRepository)
    {
        $this->dialogRepository = $dialogRepository;
    }

    /**
     * Get dialogs
     *
     * @authenticated
     *
     * @responseFile 200 responses/pm/dialogs/index_200.json
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request) : JsonResponse
    {
        return response()->json(FractalManager::collectionToFractalPaginate(
            $request,
            $this->dialogRepository->dialogsUser($request->user(), $request->validated()),
            new DialogListTransformer
        ));
    }
            
    /**
     * Get dialog id
     *
     * @authenticated
     *
     * @responseFile 200 responses/pm/dialogs/id_200.json
     * @responseFile 422 responses/pm/dialogs/id_422.json
     *
     * @param Dialog        $dialog
     * @param ShowRequest   $request
     * @return JsonResponse
     */
    public function id(IdRequest $request) : JsonResponse
    {
        $dialog = $this->dialogRepository->make($request->user(), $request->users);
        
        return response()->json(['data' => ['id' => $dialog->id]]);
    }

    /**
     * Get dialog
     *
     * @authenticated
     *
     * @responseFile 200 responses/pm/dialogs/show_200.json
     * @responseFile 401 responses/pm/dialogs/show_401.json
     * @responseFile 404 responses/pm/dialogs/show_404.json
     * 
     * @param Dialog        $dialog
     * @param ShowRequest   $request
     * @return JsonResponse
     */
    public function show(Dialog $dialog, ShowRequest $request) : JsonResponse
    {
        return response()->json(FractalManager::formatResourceFractal(
            fractal()
            ->item($dialog, new DialogTransformer())
        ));
    }
    
    /**
     * Delete dialog
     *
     * @authenticated
     *
     * @responseFile 201 responses/pm/dialogs/delete_201.json
     * @responseFile 401 responses/pm/dialogs/delete_401.json
     * @responseFile 404 responses/pm/dialogs/delete_404.json
     *
     * @param Dialog        $dialog
     * @param ShowRequest   $request
     * @return JsonResponse
     */
    public function delete(Dialog $dialog, DeleteRequest $request) : JsonResponse
    {
        return response()->json(['data' => [
            'count' => $this->dialogRepository->delete($dialog, $request->user()),
        ]]);
    }
    
}
