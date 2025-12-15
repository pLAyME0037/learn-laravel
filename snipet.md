<!-- In Controller/Component: $genders = \App\Models\Dictionary::options('gender'); -->

Scenario 1: Using Dictionary in a Blade Form (e.g., Create Student)
<select name="gender">
    @foreach($genders as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
    @endforeach
</select>

Scenario 2: Using Config in a Service (e.g., Billing)
public function calculateTuition($credits)
{
    $cost = \App\Models\SystemConfig::get('cost_per_credit', 50); // Default to 50 if not set
    return $credits * $cost;
}

Scenario 3: Checking System Status in Middleware
public function handle($request, $next)
{
    if (\App\Models\SystemConfig::isTrue('maintenance_mode')) {
        // Allow admins to bypass
        if (! auth()->user()?->hasRole('admin')) {
            abort(503);
        }
    }
    return $next($request);
}