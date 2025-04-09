<?

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function sendPush(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);

        $expoUrl = 'https://exp.host/--/api/v2/push/send';

        $response = Http::post($expoUrl, [
            'to' => $request->token,
            'title' => $request->title,
            'body' => $request->body,
            // Optional: add data for deep linking
            'data' => ['screen' => 'reminders'],
        ]);

        return response()->json([
            'success' => true,
            'response' => $response->json()
        ]);
    }
}
