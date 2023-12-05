<?php
    
    namespace App\Http\Requests;
    
    use Illuminate\Foundation\Http\FormRequest;
    
    class StoreAnnouncement extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize()
        {
            return true;
        }
        
        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            return [
                'name' => 'sometimes|required|string|max:64',
                'email' => 'sometimes|required|email',
                'tel' => 'sometimes|required|regex:/^([0-9\s\-\+\(\)]*)$/|min:11',
                'image[]' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10048',
                'option_types[]' => 'integer',
                'announcement_type_id' => 'required|integer',
                'property_type_id' => 'required|integer',
                'agent_id' => 'integer| nullable',
                'seller_id' => 'integer|nullable',
                'place_id' => 'integer|required|nullable',
                // 'street' => 'required|string|max:255',
                'coords' => 'string|nullable',
                'building' => 'required|string|max:255',
                'apartment' => 'required|string|max:255',
                'thumbnail' => 'string|max:255',
                'floor' => 'integer|nullable',
                'building_floor' => 'string|max:5|nullable',
                'rooms' => 'integer|nullable',
                'area' => 'numeric|nullable',
                'land_area' => 'numeric|nullable',
                'price' => 'numeric|nullable',
                'price_currency_id' => 'integer|nullable',
                'service_fee' => 'numeric|nullable',
                'service_fee_currency_id' => 'integer|nullable',
                'intercom' => 'string|nullable|max:100',
                'advertised' => 'integer|nullable',
                'urgent' => 'integer|nullable',
                'top' => 'integer|nullable',
                'description' => 'string|nullable',
                'professional_note' => 'string|nullable',
                'why_note' => 'string|nullable',
                'other_note' => 'string|nullable',
            ];
        }
        
       
    }
