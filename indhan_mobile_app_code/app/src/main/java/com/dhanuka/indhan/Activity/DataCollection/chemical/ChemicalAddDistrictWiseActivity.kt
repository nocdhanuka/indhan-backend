package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.Constants
import com.dhanuka.indhan.Model.ChemicalDistrictWiseDataList
import com.dhanuka.indhan.Model.FormulationResponse
import com.dhanuka.indhan.Model.ListClassResponse
import com.dhanuka.indhan.Model.UpdateChemicalDistrictWiseData
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_chemical_district_wise.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ChemicalAddDistrictWiseActivity : BaseActivity(), View.OnClickListener {
    private var classObject: ListClassResponse.Data? = null
    private var formulationObject: FormulationResponse.Data? = null
    private lateinit var preference: Preference

    private var selectedClassStr: String = ""
    private var selectClass: NumberSpinner? = null
    private var selectClassList = ArrayList<ListClassResponse.Data>()

    private var isEdit: Boolean = false
    private var selectedFormulationStr: String = ""
    private var selectFormulation: NumberSpinner? = null
    private var selectFormulationList = ArrayList<FormulationResponse.Data>()
    private var modelData: ChemicalDistrictWiseDataList.Data? = null
    private var districtName: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_district_wise)
        preference = Preference(this)
        intent?.getStringExtra("from")?.let {
            llHeaderText.text = "Molecule Data Collection - Edit District Wise"
            isEdit = true
        }
        intent?.getStringExtra("district_name")?.let {
            districtName = it
            tvDistrictName.text = "For District - $districtName"
        }
        tvFyUser.text = Utility.financialYear
        setListener()
        getClassData()
    }

    private fun setEditData() {
        intent?.getStringExtra("edit_data").let {
            modelData =
                Gson().fromJson<ChemicalDistrictWiseDataList.Data>(
                    it,
                    ChemicalDistrictWiseDataList.Data::class.java
                )
            selectedClassStr = modelData?.classname ?: ""
            val classObject: ListClassResponse.Data =
                selectClassList.find { it.name == selectedClassStr }!!
            txtChemicalSelectFormulation.visibility = View.VISIBLE
            selectedFormulationStr = modelData?.formulationname ?: ""
            txtChemicalSelectClass.text = selectedClassStr
            txtRemarks.setText(modelData?.remarks ?: "")
            getFormulation(classObject.class_id)
        }
    }

    private fun getClassData() {
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).classData
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ListClassResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ListClassResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        selectClassList.clear()
                        selectClassList.addAll(listBean.data)
                        if (isEdit) {
                            txtChemicalSelectClass.visibility = View.VISIBLE
                            llFormulationInputData.visibility = View.VISIBLE
                            setEditData()
                        }
                        setClassData()
                    }
                }
            })
    }

    private fun setClassData() {
        val districts = ArrayList<String>()
        selectClassList.forEach {
            districts.add(it.name)
        }
        selectClass =
            NumberSpinner(this, districts, "Search or Select Category")
        selectClass?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                selectedClassStr = item
                txtChemicalSelectClass.text = item
                classObject =
                    selectClassList.find { it.name == selectedClassStr }!!
                txtChemicalSelectFormulation.visibility = View.VISIBLE
                getFormulation(classObject?.class_id ?: "")
            }
        })
    }

    private fun getFormulation(classId: String?) {
        showProgress()
        val query = HashMap<String, Any>()
        query["class_id"] = classId.toString()
        query["ecn"] = preference.getString("ecn")
        query["district_id"] = intent?.getStringExtra("district_id").toString()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getFormulation(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<FormulationResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: FormulationResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        selectFormulationList.clear()
                        selectFormulationList.addAll(listBean.data)
                        setFormulationData()
                    }
                }
            })
    }

    private fun setFormulationData() {
        val districts = ArrayList<String>()
        selectFormulationList.forEach {
            districts.add(it.name)
        }
        selectFormulation =
            NumberSpinner(this, districts, "Search or Select Formulation")
        selectFormulation?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                llFormulationInputData.visibility = View.VISIBLE
                selectedFormulationStr = item
                txtChemicalSelectFormulation.text = item
                formulationObject =
                    selectFormulationList.find { it.name == selectedFormulationStr }!!
                setFormulationSelectionData(formulationObject)
            }
        })
        if (isEdit) {
            formulationObject =
                selectFormulationList.find { it.name == selectedFormulationStr }!!
            txtChemicalSelectFormulation.text = selectedFormulationStr
            setFormulationSelectionData(formulationObject)
            txtAddVolume.setText("" + modelData?.volume)
            if (!TextUtils.isEmpty(modelData?.volume ?: "")) {
                formulationObject?.nrv?.let {
                    if (!TextUtils.isEmpty(it)) {
                        val volume =
                            it.toDouble().times((modelData?.volume ?: "0").toString().toDouble())
                        txtValueTotal.text = String.format("%.1f", volume)
                    } else {
                        txtValueTotal.text = "0"
                    }
                }
            }

        }
    }

    private fun setFormulationSelectionData(formulationObject: FormulationResponse.Data?) {
        txtBrandName.text = formulationObject?.brand ?: ""
    }

    private fun setListener() {
        txtChemicalSelectClass.setOnClickListener(this)
        txtChemicalSelectFormulation.setOnClickListener(this)
        txtSubmitChemicalDistrictWise.setOnClickListener(this)

        txtAddVolume.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                if (!TextUtils.isEmpty(s.toString())) {
                    formulationObject?.nrv?.let {
                        if (!TextUtils.isEmpty(it)) {
                            val volume = it.toDouble().times(s.toString().toDouble())
                            txtValueTotal.text = String.format("%.1f", volume)
                        }
                    }
                } else {
                    txtValueTotal.text = "0"
                }
            }

            override fun afterTextChanged(s: Editable?) {
            }

        })
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtChemicalSelectClass -> {
                if (selectClassList.size > 0) {
                    selectClass?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }
                } else {
                    makeToast("No class found in district.")
                }
            }
            R.id.txtChemicalSelectFormulation -> {
                if (selectFormulationList.size > 0) {
                    selectFormulation?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }

                } else {
                    makeToast("No formulation found in selected class.")
                }
            }
            R.id.txtSubmitChemicalDistrictWise -> {
                if (!isEdit) {
                    if (!TextUtils.isEmpty(txtAddVolume.text.toString().trim())) {
                        saveDistrictWiseData()
                    } else {
                        makeToast("Volume field cannot be empty.")
                    }
                } else {
                    editDistrictWiseData()
                }

            }
        }
    }

    private fun editDistrictWiseData() {
        if (formulationObject != null && !TextUtils.isEmpty(txtAddVolume.text.toString().trim())) {

            val districtId = intent?.getStringExtra("district_id")
            val stateId = intent?.getStringExtra("state_id")
            val formulationId = formulationObject?.f_id ?: ""
            val classId = formulationObject?.class_id ?: ""

            showProgress()
            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["id"] = modelData?.id ?: "0"
            query["state_id"] = stateId.toString()
            query["district_id"] = districtId.toString()
            query["class_id"] = classId
            query["formulation_id"] = formulationId
            query["volume"] = txtAddVolume.text.toString().trim()
            if (!TextUtils.isEmpty(txtRemarks.text.toString().trim())) {
                query["remarks"] = txtRemarks.text.toString().trim()
            } else {
                query["remarks"] = "test"
            }

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .updateChemicalDistrictData(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<UpdateChemicalDistrictWiseData>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        makeToast(e.localizedMessage)
                        hideProgress()
                    }

                    override fun onNext(data: UpdateChemicalDistrictWiseData) {
                        if (data.status_code == 200) {
                            hideProgress()
                            makeToast(data.message)
                            finish()
                        }
                    }
                })
        }
    }

    private fun saveDistrictWiseData() {
        val districtId = intent?.getStringExtra("district_id")
        val stateId = intent?.getStringExtra("state_id")
        val formulationId = formulationObject?.f_id ?: ""
        val classId = formulationObject?.class_id ?: ""
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["state_id"] = stateId.toString()
        query["district_id"] = districtId.toString()
        query["class_id"] = classId.toString()
        query["formulation_id"] = formulationId.toString()
        query["volume"] = txtAddVolume.text.toString().trim()
        if (!TextUtils.isEmpty(txtRemarks.text.toString().trim())) {
            query["remarks"] = txtRemarks.text.toString().trim()
        } else {
            query["remarks"] = "test"
        }

        val bundle = Bundle()
        bundle.putString("district_id", districtId)
        bundle.putString("state_id", stateId)
        bundle.putString("district_name", tvDistrictName.text.toString())
        bundle.putSerializable("add_district_data", query)
        bundle.putString("class_data", Gson().toJson(classObject))
        bundle.putString("formulation_data", Gson().toJson(formulationObject))
        val intent = Intent(activity, ChemicalAddCropSubActivity::class.java)
        intent.putExtras(bundle)
        startActivityForResult(intent, Constants.ACTIVITY_RESULT_CROP)

    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == Constants.ACTIVITY_RESULT_CROP) {
                setResult(RESULT_OK)
                finish()
            }
        }
    }
}