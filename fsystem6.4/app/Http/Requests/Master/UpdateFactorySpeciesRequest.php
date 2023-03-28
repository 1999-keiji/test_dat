<?php

declare(strict_types=1);

namespace App\Http\Requests\Master;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Master\FactoryCyclePattern;
use App\Models\Master\FactoryPanel;
use App\ValueObjects\Enum\GrowingStage;
use App\ValueObjects\Integer\GrowingTerm;

class UpdateFactorySpeciesRequest extends FormRequest
{
    /**
     * @var \App\Models\Master\FactoryPanel
     */
    private $factory_panel;

    /**
     * @var \App\Models\Master\FactoryCyclePattern
     */
    private $factory_cycle_pattern;

    /**
     * @var \App\ValueObjects\Enum\GrowingStage
     */
    private $growing_stage;

    /**
     * @var \App\ValueObjects\Integer\GrowingTerm
     */
    private $growing_term;

    /**
     * @param  \App\Models\Master\FactoryPanel $factory_panel
     * @param  \App\Models\Master\FactoryCyclePattern $factory_cycle_pattern
     * @param  \App\ValueObjects\Enum\GrowingStage $growing_stage
     * @param  \App\ValueObjects\Integer\GrowingTerm $growing_term
     * @return void
     */
    public function __construct(
        FactoryPanel $factory_panel,
        FactoryCyclePattern $factory_cycle_pattern,
        GrowingStage $growing_stage,
        GrowingTerm $growing_term
    ) {
        $this->factory_panel = $factory_panel;
        $this->factory_cycle_pattern = $factory_cycle_pattern;
        $this->growing_stage = $growing_stage;
        $this->growing_term = $growing_term;
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
        return [
            'factory_species_name' => ['bail', 'required', 'string', "max:30"],
            'weight' => ['bail', 'required', 'integer', 'min:0', 'max:9999'],
            'can_select_on_simulation' => ['bail', 'required', 'boolean'],
            'remark' => ['bail', 'nullable', 'string', 'max:50'],
            'growing_stage_name.*' => ['bail', 'required', 'string', 'max:5'],
            'growing_stage.*' => [
                'bail',
                'required',
                Rule::in($this->growing_stage->all())
            ],
            'label_color.*' => [
                'bail',
                "required_if:growing_stage,".implode(',', $this->growing_stage->getGrowingStagesThatNeedLabelColor()),
                'string',
                'regex:/#([a-fA-F0-9]{3}){1,2}\b/i'
            ],
            'growing_term.*' => [
                'bail',
                'required',
                'integer',
                "min:{$this->growing_term->getMinimumNum()}",
                "max:{$this->growing_term->getMaximumNum()}"
            ],
            'number_of_holes.*' => [
                'bail',
                'required',
                Rule::exists($this->factory_panel->getTable(), 'number_of_holes')->where(function ($query) {
                    $query->where('factory_code', $this->route('factory')->factory_code);
                })
            ],
            'yield_rate.*' => [
                'bail',
                'required_if:growing_stage,'.implode(',', $this->growing_stage->getGrowingStagesThatNeedYieldRate()),
                'integer',
                'min:1',
                'max:100'
            ],
            'cycle_pattern_sequence_number' => ['bail', 'required', 'array'],
            'cycle_pattern_sequence_number.*' => [
                'bail',
                'required_if:growing_stage,'.implode(',', $this->growing_stage->getGrowingStagesThatNeedCyclePattern()),
                Rule::exists($this->factory_cycle_pattern->getTable(), 'sequence_number')->where(function ($query) {
                    $query->where('factory_code', $this->route('factory')->factory_code);
                })
            ]
        ];
    }
}
