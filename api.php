[1mdiff --cc routes/api.php[m
[1mindex f7974fa,2b64f72..0000000[m
[1m--- a/routes/api.php[m
[1m+++ b/routes/api.php[m
[36m@@@ -4,9 -4,9 +4,10 @@@[m [muse Illuminate\Http\Request[m
  use Illuminate\Support\Facades\Route;[m
  use App\Http\Controllers\Api\EventApiController;[m
  use App\Http\Controllers\Api\TicketApiController;[m
[32m +use App\Http\Controllers\Api\VendorApiController_cl;[m
  use App\Http\Controllers\Api\VendorApiController;[m
  use App\Http\Controllers\Api\VendorManagementApiController;[m
[32m+ use App\Http\Controllers\EventController;[m
  [m
  /*[m
  |--------------------------------------------------------------------------[m
