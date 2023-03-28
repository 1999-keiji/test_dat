<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\EndUser;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\TransportCompany;
use App\Models\Master\CollectionTime;
use App\ValueObjects\Enum\DeliveryDestinationClass;
use App\ValueObjects\Enum\FsystemStatementOfDeliveryOutputClass;
use App\ValueObjects\Enum\PrefectureCode;
use App\ValueObjects\Enum\ShipmentWayClass;
use App\ValueObjects\Enum\StatementOfDeliveryOutputClass;
use App\ValueObjects\Enum\StatementOfShipmentOutputClass;
use App\ValueObjects\String\CountryCode;
use App\ValueObjects\String\PostalCode;

class UpdateDeliveryDestinationRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\DeliveryDestination
     */
    private $delivery_destination;

    /**
     * @var \App\Model\Master\TransportCompany
     */
    private $transport_company;

    /**
     * @var \App\Model\Master\CollectionTime
     */
    private $collection_time;

    /**
     * @var \App\Models\Master\EndUser
     */
    private $end_user;

    /**
     * @var App\ValueObjects\String\CountryCode
     */
    private $country_code;

    /**
     * @var App\ValueObjects\String\PostalCode
     */
    private $postal_code;

    /**
     * @var App\ValueObjects\Enum\PrefectureCode
     */
    private $prefecture_code;

    /**
     * @var App\ValueObjects\Enum\StatementOfDeliveryOutputClass
     */
    private $statement_of_delivery_output_class;

    /**
     * @var App\ValueObjects\Enum\ShipmentWayClass
     */
    private $shipment_way_class;

    /**
     * @var \App\Models\Master\DeliveryDestinationClass
     */
    private $delivery_destination_class;

    /**
     * @var \App\Models\Master\FsystemStatementOfDeliveryOutputClass
     */
    private $fsystem_statement_of_delivery_output_class;

    /**
     * @var \App\Models\Master\StatementOfShipmentOutputClass
     */
    private $statement_of_shipment_output_class;

    /**
     * @var array
     */
    protected $on_off_checkboxes;

    /**
     * @param  \App\Models\Master\DeliveryDestination
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @param  \App\Models\Master\CollectionTime $collection_time
     * @param  \App\Models\Master\EndUser $end_user
     * @param  \App\ValueObjects\String\CountryCode
     * @param  \App\ValueObjects\String\PostalCode
     * @param  \App\ValueObjects\Enum\PrefectureCode
     * @param  \App\ValueObjects\Enum\StatementOfDeliveryOutputClass
     * @param  \App\ValueObjects\Enum\ShipmentWayClass
     * @param  \App\ValueObjects\Enum\DeliveryDestinationClass
     * @param  \App\ValueObjects\Enum\FsystemStatementOfDeliveryOutputClass
     * @param  \App\ValueObjects\Enum\StatementOfShipmentOutputClass
     * @return void
     */
    public function __construct(
        DeliveryDestination $delivery_destination,
        TransportCompany $transport_company,
        CollectionTime $collection_time,
        EndUser $end_user,
        CountryCode $country_code,
        PostalCode $postal_code,
        PrefectureCode $prefecture_code,
        StatementOfDeliveryOutputClass $statement_of_delivery_output_class,
        ShipmentWayClass $shipment_way_class,
        DeliveryDestinationClass $delivery_destination_class,
        FsystemStatementOfDeliveryOutputClass $fsystem_statement_of_delivery_output_class,
        StatementOfShipmentOutputClass $statement_of_shipment_output_class
    ) {
        $this->delivery_destination = $delivery_destination;
        $this->transport_company = $transport_company;
        $this->collection_time = $collection_time;
        $this->end_user = $end_user;
        $this->country_code = $country_code;
        $this->postal_code = $postal_code;
        $this->prefecture_code = $prefecture_code;
        $this->statement_of_delivery_output_class = $statement_of_delivery_output_class;
        $this->shipment_way_class = $shipment_way_class;
        $this->delivery_destination_class = $delivery_destination_class;
        $this->fsystem_statement_of_delivery_output_class = $fsystem_statement_of_delivery_output_class;
        $this->statement_of_shipment_output_class = $statement_of_shipment_output_class;

        $this->on_off_checkboxes = $this->delivery_destination->getWillCastAsBoolean();
    }

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
        $updatable = $this->route('delivery_destination')->creating_type->getUpdatableCreatingTypes();
        $rules = [
            'delivery_destination_name' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:50'
            ],
            'delivery_destination_name2' => ['bail', 'nullable', 'string', 'max:50'],
            'delivery_destination_abbreviation' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:20'
            ],
            'delivery_destination_name_kana' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'max:30',
                'hankana'
            ],
            'delivery_destination_name_english' => [
                'bail',
                'nullable',
                'max:65',
                'alpha_period_dash'
            ],
            'country_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                "min:{$this->country_code->getMinLength()}",
                "max:{$this->country_code->getMaxLength()}",
                "regex:{$this->country_code->getRegexPattern()}"
            ],
            'postal_code' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                "max:{$this->postal_code->getMaxLength()}",
                "regex:{$this->postal_code->getRegexPattern()}"
            ],
            'prefecture_code' => [
                'bail',
                'nullable',
                'required_if:creating_type,'.implode(',', $updatable),
                "required_if:country_code,{$this->prefecture_code->getJoinedRequirePrefectureCodeList()}",
                Rule::in($this->prefecture_code->all())
            ],
            'address' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:50'
            ],
            'address2' => ['bail', 'nullable', 'string', 'max:50'],
            'address3' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address2' => ['bail', 'nullable', 'string', 'max:50'],
            'abroad_address3' => ['bail', 'nullable', 'string', 'max:50'],
            'phone_number' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'string',
                'max:20',
                'regex:/\A[0-9-]+\z/'
            ],
            'extension_number' => ['bail', 'nullable', 'string', 'max:15', 'regex:/\A[0-9-]+\z/'],
            'fax_number' => ['bail', 'nullable', 'string', 'max:15', 'regex:/\A[0-9-]+\z/'],
            'mail_address' => ['bail', 'nullable', 'string', 'max:250', 'email',],
            'staff_abbreviation' => ['bail', 'nullable', 'string', 'max:10'],
            'statement_of_delivery_message' => ['bail', 'nullable', 'string', 'max:40'],
            'statement_of_delivery_output_class' => [
                'bail',
                'nullable',
                Rule::in($this->statement_of_delivery_output_class->all())
            ],
            'shipping_label_unnecessary_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'export_target_flag' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                'boolean'
            ],
            'shipment_way_class' => [
                'bail',
                'nullable',
                Rule::in($this->shipment_way_class->all())
            ],
            'delivery_destination_class' => [
                'bail',
                'required_if:creating_type,'.implode(',', $updatable),
                Rule::in($this->delivery_destination_class->all())
            ],
            'transport_company_code' => [
                'bail',
                'required',
                "exists:{$this->transport_company->getTable()}"
            ],
            'collection_time_sequence_number' => [
                'bail',
                'required',
                Rule::exists($this->collection_time->getTable(), 'sequence_number')->where(function ($query) {
                    $query->where('transport_company_code', $this->transport_company_code);
                })
            ],
            'collection_request_remark' => ['bail', 'nullable', 'string', 'max:50'],
            'end_user_code' => [
                'bail',
                'required',
                "exists:{$this->end_user->getTable()}"
            ],
            'fsystem_statement_of_delivery_output_class' => [
                'bail',
                'required',
                Rule::in($this->fsystem_statement_of_delivery_output_class->all())
            ],
            'statement_of_shipment_output_class' => [
                'bail',
                'required',
                Rule::in($this->statement_of_shipment_output_class->all())
            ],
            'needs_to_subtract_printing_delivery_date' => ['bail', 'required', 'boolean'],
            'can_display' => ['bail', 'required', 'boolean'],
            'remark' => ['bail', 'nullable', 'string', 'max:255']
        ];

        return $rules + $this->reservedRules();
    }
}
