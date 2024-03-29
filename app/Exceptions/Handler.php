<?php namespace App\Exceptions;

use Exception;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($e instanceof ServiceException)
        {
            #todo: json format temp
            return $e->getInfo();
        }
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {

        if($e instanceof NotFoundHttpException){
            \Log::info('404 '.$request->url());
            return response('Not found.', 404);
        }
        // Service Not Found 
        else if ($e instanceof ServiceException)
        {
            #todo: json format temp
            return $e->getInfo();
        }
        //todo remove
        else if ($e->getCode() == -1)
        {
            dd($e);
        }

        return parent::render($request, $e);
    }

}
