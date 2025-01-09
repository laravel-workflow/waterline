<?php
declare(strict_types=1);

namespace Waterline\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use SplFileObject;
use Workflow\Models\StoredWorkflowException;
use Workflow\Serializers\Y;

/**
 * @mixin StoredWorkflowException
 */
class StoredWorkflowExceptionResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        $code = '';
        $exception = $this->exception;

        $unserialized = Y::unserialize($exception);
        if (is_array($unserialized)
            && array_key_exists('class', $unserialized)
            && is_subclass_of($unserialized['class'], \Throwable::class)
            && file_exists($unserialized['file'])
        ) {
            $file = new SplFileObject($unserialized['file']);
            $file->seek($unserialized['line'] - 4);
            for ($line = 0; $line < 7; ++$line) {
                $code .= $file->current();
                $file->next();
                if ($file->eof()) break;
            }
            $code = rtrim($code);
            $exception = serialize($unserialized);
        }

        return [
            "id" => $this->id,
            "code" => $code,
            "exception" => $exception,
            "class" => $this->class,
            "created_at" => $this->created_at,
        ];
    }
}
