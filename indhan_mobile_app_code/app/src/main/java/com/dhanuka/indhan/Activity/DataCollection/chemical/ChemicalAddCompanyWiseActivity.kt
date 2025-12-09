package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Model.FormulationResponse
import com.dhanuka.indhan.Model.ListClassResponse
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_chemical_company_wise.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ChemicalAddCompanyWiseActivity : BaseActivity() {
    private lateinit var preference: Preference
    private lateinit var formulationObject: FormulationResponse.Data
    private lateinit var classObject: ListClassResponse.Data
    private lateinit var selectedClassStr: String
    private lateinit var selectClass: NumberSpinner
    private var selectClassList = ArrayList<ListClassResponse.Data>()
    private lateinit var selectedFormulationStr: String
    private lateinit var selectFormulation: NumberSpinner
    private var selectFormulationList = ArrayList<FormulationResponse.Data>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_company_wise)
        preference = Preference(this)
        setListener()
        getClassData()
    }

    private fun setListener() {
        txtChemicalSelectClass.setOnClickListener(this)
        txtChemicalSelectFormulation.setOnClickListener(this)
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
        selectClass.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                selectedClassStr = item
                txtChemicalSelectClass.text = item
                classObject =
                    selectClassList.find { it.name == selectedClassStr }!!
                txtChemicalSelectFormulation.visibility = View.VISIBLE
                getFormulation(classObject.class_id)
            }
        })
    }

    private fun getFormulation(classId: String?) {
        showProgress()
        val query = HashMap<String, Any>()
        query["class_id"] = classId.toString()
        query["ecn"]  = preference.getString("ecn")
        query["district_id"]  = intent?.getStringExtra("district_id").toString()
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
        selectFormulation.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                selectedFormulationStr = item
                txtChemicalSelectFormulation.text = item
                formulationObject =
                    selectFormulationList.find { it.name == selectedFormulationStr }!!
                navigateToNextScreen()
            }
        })
    }

    private fun navigateToNextScreen() {
        val districtId = intent?.getStringExtra("district_id")
        val stateId = intent?.getStringExtra("state_id")
        val bundle = Bundle()
        bundle.putString("district_id", districtId)
        bundle.putString("state_id", stateId)
        bundle.putString("class_data", Gson().toJson(classObject))
        bundle.putString("formulation_data", Gson().toJson(formulationObject))
        launchIntent(ChemicalAddCompanySubActivity::class.java, bundle, true)
    }


    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtChemicalSelectClass -> {
                if (!selectClass.isEnable) {
                    selectClass.showSpinerDialog()
                }
            }
            R.id.txtChemicalSelectFormulation -> {
                if (!selectFormulation.isEnable) {
                    selectFormulation.showSpinerDialog()
                }
            }
        }
    }
}