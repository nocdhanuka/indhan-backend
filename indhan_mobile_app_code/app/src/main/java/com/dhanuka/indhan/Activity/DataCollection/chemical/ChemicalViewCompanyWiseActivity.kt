package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.os.Bundle
import android.text.TextUtils
import android.view.MenuInflater
import android.view.MenuItem
import android.view.View
import android.widget.PopupMenu
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.DataCollection.chemical.adapter.ChemicalCompanyWiseItemAdapter
import com.dhanuka.indhan.Model.*
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_chemical_view_company_wise.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ChemicalViewCompanyWiseActivity : BaseActivity(),
    ChemicalCompanyWiseItemAdapter.AddNewItemListAdapter, View.OnClickListener,
    PopupMenu.OnMenuItemClickListener {
    private val companyModel = CompanyRequestModel()
    private lateinit var preference: Preference
    private var chemicalDataCompanyWise = ArrayList<ChemicalCompanyWiseDataList.Data>()
    private var categoryList = ArrayList<CompanyListResponse.Data>()
    private lateinit var listAdapter: ChemicalCompanyWiseItemAdapter
    private val companyAllData = ArrayList<CompanyModel>()
    private val filterCompanyAllData = ArrayList<CompanyModel>()
    private lateinit var formulationData: FormulationResponse.Data
    private lateinit var classData: ListClassResponse.Data
    private var totalPercentage = 0.0
    private var districtName = ""
    var districtId = ""
    var stateId = ""
    private var index: Int = 0
    private var formulationStr: String = ""
    private var listFormulation = ArrayList<String>()
    private var selectFormulation: NumberSpinner? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_view_company_wise)
        txtSubmitChemicalCompanyWise.setOnClickListener(this)
        imgFilter.setOnClickListener(this)
        txtFilterChemical.setOnClickListener(this)
        preference = Preference(this)
        getIntentData()
    }

    fun showPopup(v: View?) {
        val popup = PopupMenu(this, v)
        val inflater: MenuInflater = popup.menuInflater
        popup.setOnMenuItemClickListener(this)
        inflater.inflate(R.menu.menu, popup.menu)
        popup.show()
    }

    override fun onMenuItemClick(item: MenuItem): Boolean {
        return when (item.itemId) {
            R.id.company -> {
                false
            }
            R.id.crop -> {
                val bundle = Bundle();
                bundle.putString("district_id", districtId)
                bundle.putString("state_id", stateId)
                bundle.putString("district_name", districtName)
                launchIntent(ChemicalViewCropWiseActivity::class.java, bundle, true)
                true
            }
            R.id.district -> {
                val bundle = Bundle();
                bundle.putString("district_id", districtId)
                bundle.putString("state_id", stateId)
                bundle.putString("district_name", districtName)
                launchIntent(ChemicalViewDistrictWiseActivity::class.java, bundle, true)
                true
            }
            else -> false
        }
    }


    private fun getIntentData() {
        tvFyUser.text = Utility.financialYear
        intent?.getStringExtra("district_name")?.let {
            districtName = it
            tvDistrictName.text = "For District - $districtName"
        }
        intent?.getStringExtra("district_id")?.let {
            districtId = it
        }
        intent?.getStringExtra("state_id")?.let {
            stateId = it
        }
        getChemicalCompanyWise()
    }

    private fun getChemicalCompanyWise() {
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .chemicalCompanyWiseRecord(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ChemicalCompanyWiseDataList>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ChemicalCompanyWiseDataList) {
                    if (listBean.status_code == 200) {
                        chemicalDataCompanyWise.clear()
                        if (listBean.data.size > 0) {
                            listBean.data.forEach {
                                if (it.district.equals(districtName, ignoreCase = true)) {
                                    chemicalDataCompanyWise.add(it)
                                    listFormulation.add(it.formulationname)
                                }
                            }
                        }
                        if (chemicalDataCompanyWise.size > 0) {
                            selectFormulation =
                                NumberSpinner(
                                    this@ChemicalViewCompanyWiseActivity,
                                    listFormulation,
                                    "Search or select formulation"
                                )
                            selectFormulation?.bindOnSpinerListener(object : OnSpinerItemClick {
                                override fun onClick(item: String, position: Int) {
                                    formulationStr = item
                                    txtFilterChemical.text = item
                                    for ((pos) in chemicalDataCompanyWise.withIndex()) {
                                        if (chemicalDataCompanyWise[pos].formulationname.equals(
                                                item,
                                                ignoreCase = true
                                            )
                                        ) {
                                            index = pos
                                            txtFilterChemical.text = chemicalDataCompanyWise[index].formulationname.toString()
                                            setAdapter()
//                                            getClassData(
//                                                chemicalDataCompanyWise[pos].classname.toString(),
//                                                chemicalDataCompanyWise[pos].formulationname.toString()
//                                            )
                                            break
                                        }
                                    }
                                }
                            })
                            getCompanyData()
                            txtFilterChemical.text = chemicalDataCompanyWise[index].formulationname.toString()
//                            getClassList(chemicalDataCompanyWise[index])
                        }
                    } else {
                        if (listBean.message != null) {
                            makeToast(listBean.message)
                        }
                        hideProgress()
                    }
                }
            })
    }

    private fun getClassList(data: ChemicalCompanyWiseDataList.Data) {
        txtFilterChemical.text = data.formulationname.toString()
        getClassData(data.classname.toString(), data.formulationname.toString())
    }

    private fun getClassData(clasName: String, formulationName: String) {
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
                        if (listBean.data.size > 0) {
                            for ((index) in listBean.data.withIndex()) {
                                if (listBean.data[index].name.equals(clasName, ignoreCase = true)) {
                                    classData = listBean.data[index]
                                    getFormulation(classData.class_id, formulationName)
                                    break
                                }
                            }
                        }
                    } else {
                        if (listBean.message != null) {
                            makeToast(listBean.message)
                        }
                        hideProgress()
                    }
                }
            })
    }

    private fun getFormulation(classId: String?, formulationName: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["class_id"] = classId.toString()
        query["ecn"]  = preference.getString("ecn")
        query["district_id"]  = districtId
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
                        if (listBean.data.size > 0) {
                            for ((index) in listBean.data.withIndex()) {
                                if (listBean.data[index].name.equals(
                                        formulationName,
                                        ignoreCase = true
                                    )
                                ) {
                                    formulationData = listBean.data[index]
                                    getCompanyData()
                                    setAdapter()
                                    break
                                }
                            }
                        }
                    } else {
                        if (listBean.message != null) {
                            makeToast(listBean.message)
                        }
                        hideProgress()
                    }
                }
            })
    }

    private fun getCompanyData() {
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).companyList
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CompanyListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: CompanyListResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        categoryList.clear()
                        categoryList.addAll(listBean.data)
                        setAdapter()
                    }
                }
            })
    }

    private fun setAdapter() {
        companyAllData.clear()
        val categories = ArrayList<String>()
        categoryList.forEach {
            categories.add(it.name)
            when {
                it.name.trim().equals("ADAMA", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].adama != null && chemicalDataCompanyWise[index].adama.isNotEmpty()) chemicalDataCompanyWise[index].adama.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("ATUL", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].atul != null && chemicalDataCompanyWise[index].atul.isNotEmpty()) chemicalDataCompanyWise[index].atul.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("BAYER", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].bayer != null && chemicalDataCompanyWise[index].bayer.isNotEmpty()) chemicalDataCompanyWise[index].bayer.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("BASF", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].basf != null && chemicalDataCompanyWise[index].basf.isNotEmpty()) chemicalDataCompanyWise[index].basf.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("BHARAT CERTIS", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].bharat_certis != null && chemicalDataCompanyWise[index].bharat_certis.isNotEmpty()) chemicalDataCompanyWise[index].bharat_certis.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("BIO-STADT", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].bio_stadt != null && chemicalDataCompanyWise[index].bio_stadt.isNotEmpty()) chemicalDataCompanyWise[index].bio_stadt.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("CHEMINOVA", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].cheminova != null && chemicalDataCompanyWise[index].cheminova.isNotEmpty()) chemicalDataCompanyWise[index].cheminova.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("COROMANDAL INTER LTD", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].coromandal_inter_ltd != null && chemicalDataCompanyWise[index].coromandal_inter_ltd.isNotEmpty()) chemicalDataCompanyWise[index].coromandal_inter_ltd.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("COROMANDEL AGRI CO", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].coromandel_agri_co != null && chemicalDataCompanyWise[index].coromandel_agri_co.isNotEmpty()) chemicalDataCompanyWise[index].coromandel_agri_co.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("Crystal", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].crystal != null && chemicalDataCompanyWise[index].crystal.isNotEmpty()) chemicalDataCompanyWise[index].crystal.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("CORTEVA", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].corteva != null && chemicalDataCompanyWise[index].corteva.isNotEmpty()) chemicalDataCompanyWise[index].corteva.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("DEVIDAYAL (SALES)", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].devidayal != null && chemicalDataCompanyWise[index].devidayal.isNotEmpty()) chemicalDataCompanyWise[index].devidayal.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("DHANUKA AGRITECH", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].dhanuka_agritech != null && chemicalDataCompanyWise[index].dhanuka_agritech.isNotEmpty()) chemicalDataCompanyWise[index].dhanuka_agritech.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("EXCEL CROP CARE", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].excel_crop_care != null && chemicalDataCompanyWise[index].excel_crop_care.isNotEmpty()) chemicalDataCompanyWise[index].excel_crop_care.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("F.I.L", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].fil != null && chemicalDataCompanyWise[index].fil.isNotEmpty()) chemicalDataCompanyWise[index].fil.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("F.M.C", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].fmc != null && chemicalDataCompanyWise[index].fmc.isNotEmpty()) chemicalDataCompanyWise[index].fmc.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("GHARDA CHEMICALS", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].gharda_chemicals != null && chemicalDataCompanyWise[index].gharda_chemicals.isNotEmpty()) chemicalDataCompanyWise[index].gharda_chemicals.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("GODREJ AGROVET LTD", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].godrej_agrovet_ltd != null && chemicalDataCompanyWise[index].godrej_agrovet_ltd.isNotEmpty()) chemicalDataCompanyWise[index].godrej_agrovet_ltd.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("G.S.P", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].gsp != null && chemicalDataCompanyWise[index].gsp.isNotEmpty()) chemicalDataCompanyWise[index].gsp.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("HINDUSTAN INSECTICIDES LTD", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].hindustan_insecticides_ltd != null && chemicalDataCompanyWise[index].hindustan_insecticides_ltd.isNotEmpty()) chemicalDataCompanyWise[index].hindustan_insecticides_ltd.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("HINDUSTAN PULVERISING", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].hindustan_pulverising != null && chemicalDataCompanyWise[index].hindustan_pulverising.isNotEmpty()) chemicalDataCompanyWise[index].hindustan_pulverising.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("HPM", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].hpm != null && chemicalDataCompanyWise[index].hpm.isNotEmpty()) chemicalDataCompanyWise[index].hpm.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("HERANBA", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].heranba != null && chemicalDataCompanyWise[index].heranba.isNotEmpty()) chemicalDataCompanyWise[index].heranba.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("INDOFIL", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].indofil != null && chemicalDataCompanyWise[index].indofil.isNotEmpty()) chemicalDataCompanyWise[index].indofil.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("INDO GULF", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].indo_gulf != null && chemicalDataCompanyWise[index].indo_gulf.isNotEmpty()) chemicalDataCompanyWise[index].indo_gulf.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("INSECTICIDES INDIA LTD", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].insecticides_india_ltd != null && chemicalDataCompanyWise[index].insecticides_india_ltd.isNotEmpty()) chemicalDataCompanyWise[index].insecticides_india_ltd.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("ICHIBAN", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].ichiban != null && chemicalDataCompanyWise[index].ichiban.isNotEmpty()) chemicalDataCompanyWise[index].ichiban.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("I.P.L", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].ipl != null && chemicalDataCompanyWise[index].ipl.isNotEmpty()) chemicalDataCompanyWise[index].ipl.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("J.U. AGRI SCIENCE", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].ju_agri_science != null && chemicalDataCompanyWise[index].ju_agri_science.isNotEmpty()) chemicalDataCompanyWise[index].ju_agri_science.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("JUBILANT", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].jubilant != null && chemicalDataCompanyWise[index].jubilant.isNotEmpty()) chemicalDataCompanyWise[index].jubilant.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("KRISHI RASAYAN", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].krishi_rasayan != null && chemicalDataCompanyWise[index].krishi_rasayan.isNotEmpty()) chemicalDataCompanyWise[index].krishi_rasayan.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("MEGHMANI", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].meghmani != null && chemicalDataCompanyWise[index].meghmani.isNotEmpty()) chemicalDataCompanyWise[index].meghmani.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("NAGARJUNA AGRI CHEM", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].nagarjuna_agri_chem != null && chemicalDataCompanyWise[index].nagarjuna_agri_chem.isNotEmpty()) chemicalDataCompanyWise[index].nagarjuna_agri_chem.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("NICHINO", ignoreCase = true) -> {

                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].nichino != null && chemicalDataCompanyWise[index].nichino.isNotEmpty()) chemicalDataCompanyWise[index].nichino.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("MULTIPLEX", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].multiplex != null && chemicalDataCompanyWise[index].multiplex.isNotEmpty()) chemicalDataCompanyWise[index].multiplex.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("MAHINDRA", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].mahindra != null && chemicalDataCompanyWise[index].mahindra.isNotEmpty()) chemicalDataCompanyWise[index].mahindra.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("P.I. INDUSTRIES", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].pi_industries != null && chemicalDataCompanyWise[index].pi_industries.isNotEmpty()) chemicalDataCompanyWise[index].pi_industries.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("PARIJAT", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].parijat != null && chemicalDataCompanyWise[index].parijat.isNotEmpty()) chemicalDataCompanyWise[index].parijat.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("PUNJAB CHEMICALS", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].punjab_chemicals != null && chemicalDataCompanyWise[index].punjab_chemicals.isNotEmpty()) chemicalDataCompanyWise[index].punjab_chemicals.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("RALLIS", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].rallis != null && chemicalDataCompanyWise[index].rallis.isNotEmpty()) chemicalDataCompanyWise[index].rallis.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("SAFEX", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].safex != null && chemicalDataCompanyWise[index].safex.isNotEmpty()) chemicalDataCompanyWise[index].safex.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("SHIVALIK CROP", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].shivalik_crop != null && chemicalDataCompanyWise[index].shivalik_crop.isNotEmpty()) chemicalDataCompanyWise[index].shivalik_crop.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("SUDARSHAN CHEMICAL", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].sudarshan_chemical != null && chemicalDataCompanyWise[index].sudarshan_chemical.isNotEmpty()) chemicalDataCompanyWise[index].sudarshan_chemical.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("SULPHUR MILLS", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].sulphur_mills != null && chemicalDataCompanyWise[index].sulphur_mills.isNotEmpty()) chemicalDataCompanyWise[index].sulphur_mills.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("SUMITOMO", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].sumitomo != null && chemicalDataCompanyWise[index].sumitomo.isNotEmpty()) chemicalDataCompanyWise[index].sumitomo.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("SWAL CORPORATION", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].swal_corporation != null && chemicalDataCompanyWise[index].swal_corporation.isNotEmpty()) chemicalDataCompanyWise[index].swal_corporation.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("SYNGENTA", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].syngenta != null && chemicalDataCompanyWise[index].syngenta.isNotEmpty()) chemicalDataCompanyWise[index].syngenta.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("UPL Ltd", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].upl_ltd != null && chemicalDataCompanyWise[index].upl_ltd.isNotEmpty()) chemicalDataCompanyWise[index].upl_ltd.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals(
                    "WILLOWOOD CROP SCIENCES PVT LTD",
                    ignoreCase = true
                ) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].willowood_crop_sciences != null && chemicalDataCompanyWise[index].willowood_crop_sciences.isNotEmpty()) chemicalDataCompanyWise[index].willowood_crop_sciences.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim()
                    .equals("ZUARI AGRO CHEMICALS", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].zuari_agro_chemicals != null && chemicalDataCompanyWise[index].zuari_agro_chemicals.isNotEmpty()) chemicalDataCompanyWise[index].zuari_agro_chemicals.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
                it.name.trim().equals("OTHERS", ignoreCase = true) -> {
                    val companyModel = CompanyModel()
                    companyModel.categoryData = it
                    companyModel.volumeData =
                        if (chemicalDataCompanyWise[index].others != null && chemicalDataCompanyWise[index].others.isNotEmpty()) chemicalDataCompanyWise[index].others.toDouble() else 0.0
                    companyAllData.add(companyModel)
                }
            }
        }
        filterCompanyAllData.clear()
        totalPercentage = 0.0
        companyAllData.forEach {
            if (it.volumeData != 0.0) {
                filterCompanyAllData.add(it)
                totalPercentage += it.volumeData
            }
        }
//        txtChemicalCompanySelectedClass.text = classData.name
//        tvFormulationName.text = formulationData.name
//        tvFormulationVolume.text = "${formulationData.volume} Kg / Ltr"
//        tvFormulationTotal.text = "Value - Rs.${formulationData.value}"
        if (totalPercentage == 100.0) {
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.progess_green))
        } else {
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.red))
        }
        tvTotalValuePercentage.text =
            "Total " + String.format("%.1f", totalPercentage) + "%"

        val linearLayoutManager =
            LinearLayoutManager(
                this@ChemicalViewCompanyWiseActivity,
                LinearLayoutManager.VERTICAL,
                false
            )

        listAdapter = ChemicalCompanyWiseItemAdapter(
            filterCompanyAllData,
            categories,
            this@ChemicalViewCompanyWiseActivity
        )
        rcvCompanyViewItems.layoutManager = linearLayoutManager
        rcvCompanyViewItems.adapter = listAdapter
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtSubmitChemicalCompanyWise -> {
                updateCompanyWiseData()
            }
            R.id.imgFilter -> {
                showPopup(v)
            }
            R.id.txtFilterChemical -> {
                if (selectFormulation?.isEnable == false) {
                    selectFormulation?.showSpinerDialog()
                }
            }
        }
    }

    private fun updateCompanyWiseData() {
        showProgress()
        filterCompanyAllData.forEach {
            if (it.categoryData != null)
                if (!TextUtils.isEmpty(it.categoryData.name)) {
                    when {
                        it.categoryData.name.trim().equals("ADAMA", ignoreCase = true) -> {
                            companyModel.adama = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("ATUL", ignoreCase = true) -> {
                            companyModel.atul = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("BAYER", ignoreCase = true) -> {
                            companyModel.bayer = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("BASF", ignoreCase = true) -> {
                            companyModel.basf = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("BHARAT CERTIS", ignoreCase = true) -> {
                            companyModel.bharat_certis = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("BIO-STADT", ignoreCase = true) -> {
                            companyModel.bio_stadt = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("CHEMINOVA", ignoreCase = true) -> {
                            companyModel.cheminova = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("COROMANDAL INTER LTD", ignoreCase = true) -> {
                            companyModel.coromandal_inter_ltd = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("COROMANDEL AGRI CO", ignoreCase = true) -> {
                            companyModel.coromandel_agri_co = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("Crystal", ignoreCase = true) -> {
                            companyModel.crystal = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("CORTEVA", ignoreCase = true) -> {
                            companyModel.corteva = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("DEVIDAYAL (SALES)", ignoreCase = true) -> {
                            companyModel.devidayal = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("DHANUKA AGRITECH", ignoreCase = true) -> {
                            companyModel.dhanuka_agritech = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("EXCEL CROP CARE", ignoreCase = true) -> {
                            companyModel.excel_crop_care = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("F.I.L", ignoreCase = true) -> {
                            companyModel.fil = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("F.M.C", ignoreCase = true) -> {
                            companyModel.fmc = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("GHARDA CHEMICALS", ignoreCase = true) -> {
                            companyModel.gharda_chemicals = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("GODREJ AGROVET LTD", ignoreCase = true) -> {
                            companyModel.godrej_agrovet_ltd = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("G.S.P", ignoreCase = true) -> {
                            companyModel.gsp = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("HINDUSTAN INSECTICIDES LTD", ignoreCase = true) -> {
                            companyModel.hindustan_insecticides_ltd = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("HINDUSTAN PULVERISING", ignoreCase = true) -> {
                            companyModel.hindustan_pulverising = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("HPM", ignoreCase = true) -> {
                            companyModel.hpm = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("HERANBA", ignoreCase = true) -> {
                            companyModel.heranba = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("INDOFIL", ignoreCase = true) -> {
                            companyModel.indofil = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("INDO GULF", ignoreCase = true) -> {
                            companyModel.indo_gulf = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("INSECTICIDES INDIA LTD", ignoreCase = true) -> {
                            companyModel.insecticides_india_ltd = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("ICHIBAN", ignoreCase = true) -> {
                            companyModel.ichiban = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("I.P.L", ignoreCase = true) -> {
                            companyModel.ipl = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("J.U. AGRI SCIENCE", ignoreCase = true) -> {
                            companyModel.ju_agri_science = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("JUBILANT", ignoreCase = true) -> {
                            companyModel.jubilant = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("KRISHI RASAYAN", ignoreCase = true) -> {
                            companyModel.krishi_rasayan = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("MEGHMANI", ignoreCase = true) -> {
                            companyModel.meghmani = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("NAGARJUNA AGRI CHEM", ignoreCase = true) -> {
                            companyModel.nagarjuna_agri_chem = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("NICHINO", ignoreCase = true) -> {
                            companyModel.nichino = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("MULTIPLEX", ignoreCase = true) -> {
                            companyModel.multiplex = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("MAHINDRA", ignoreCase = true) -> {
                            companyModel.mahindra = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("P.I. INDUSTRIES", ignoreCase = true) -> {
                            companyModel.pi_industries = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("PARIJAT", ignoreCase = true) -> {
                            companyModel.parijat = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("PUNJAB CHEMICALS", ignoreCase = true) -> {
                            companyModel.punjab_chemicals = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("RALLIS", ignoreCase = true) -> {
                            companyModel.rallis = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("SAFEX", ignoreCase = true) -> {
                            companyModel.safex = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("SHIVALIK CROP", ignoreCase = true) -> {
                            companyModel.shivalik_crop = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("SUDARSHAN CHEMICAL", ignoreCase = true) -> {
                            companyModel.sudarshan_chemical = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("SULPHUR MILLS", ignoreCase = true) -> {
                            companyModel.sulphur_mills = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("SUMITOMO", ignoreCase = true) -> {
                            companyModel.sumitomo = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("SWAL CORPORATION", ignoreCase = true) -> {
                            companyModel.swal_corporation = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("SYNGENTA", ignoreCase = true) -> {
                            companyModel.syngenta = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("UPL Ltd", ignoreCase = true) -> {
                            companyModel.upl_ltd = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals(
                            "WILLOWOOD CROP SCIENCES PVT LTD",
                            ignoreCase = true
                        ) -> {
                            companyModel.willowood_crop_sciences = it.volumeData.toString()
                        }
                        it.categoryData.name.trim()
                            .equals("ZUARI AGRO CHEMICALS", ignoreCase = true) -> {
                            companyModel.zuari_agro_chemicals = it.volumeData.toString()
                        }
                        it.categoryData.name.trim().equals("OTHERS", ignoreCase = true) -> {
                            companyModel.others = it.volumeData.toString()
                        }
                    }
                }
        }
        if (chemicalDataCompanyWise[index].id != null) {
            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["id"] = chemicalDataCompanyWise[index].id
            query["state_id"] = stateId
            query["district_id"] = districtId
            query["class_id"] = classData.class_id
            query["formulation_id"] = formulationData.f_id
            query["adama"] = companyModel.adama
            query["atul"] = companyModel.atul
            query["bayer"] = companyModel.bayer
            query["zuari_agro_chemicals"] = companyModel.zuari_agro_chemicals
            query["basf"] = companyModel.basf
            query["bharat_certis"] = companyModel.bharat_certis
            query["bio_stadt"] = companyModel.bio_stadt
            query["cheminova"] = companyModel.cheminova
            query["coromandal_inter_ltd"] = companyModel.coromandal_inter_ltd
            query["coromandel_agri_co"] = companyModel.coromandel_agri_co
            query["crystal"] = companyModel.crystal
            query["corteva"] = companyModel.corteva
            query["devidayal"] = companyModel.devidayal
            query["dhanuka_agritech"] = companyModel.dhanuka_agritech
            query["fil"] = companyModel.fil
            query["fmc"] = companyModel.fmc
            query["gharda_chemicals"] = companyModel.gharda_chemicals
            query["godrej_agrovet_ltd"] = companyModel.godrej_agrovet_ltd
            query["gsp"] = companyModel.gsp
            query["hindustan_insecticides_ltd"] =
                companyModel.hindustan_insecticides_ltd
            query["hindustan_pulverising"] = companyModel.hindustan_pulverising
            query["hpm"] = companyModel.hpm
            query["heranba"] = companyModel.heranba
            query["indofil"] = companyModel.indofil
            query["indo_gulf"] = companyModel.indo_gulf
            query["insecticides_india_ltd"] = companyModel.insecticides_india_ltd
            query["ichiban"] = companyModel.ichiban
            query["ipl"] = companyModel.ipl
            query["ju_agri_science"] = companyModel.ju_agri_science
            query["excel_crop_care"] = companyModel.excel_crop_care
            query["jubilant"] = companyModel.jubilant
            query["krishi_rasayan"] = companyModel.krishi_rasayan
            query["meghmani"] = companyModel.meghmani
            query["nagarjuna_agri_chem"] = companyModel.nagarjuna_agri_chem
            query["nichino"] = companyModel.nichino
            query["multiplex"] = companyModel.multiplex
            query["mahindra"] = companyModel.mahindra
            query["pi_industries"] = companyModel.pi_industries
            query["parijat"] = companyModel.parijat
            query["punjab_chemicals"] = companyModel.punjab_chemicals
            query["rallis"] = companyModel.rallis
            query["safex"] = companyModel.safex
            query["shivalik_crop"] = companyModel.shivalik_crop
            query["sudarshan_chemical"] = companyModel.sudarshan_chemical
            query["sulphur_mills"] = companyModel.sulphur_mills
            query["sumitomo"] = companyModel.sumitomo
            query["swal_corporation"] = companyModel.swal_corporation
            query["syngenta"] = companyModel.syngenta
            query["upl_ltd"] = companyModel.upl_ltd
            query["willowood_crop_sciences"] = companyModel.willowood_crop_sciences
            query["others"] = companyModel.others
            query["grand_total"] = totalPercentage.toString()
            query["check"] = formulationData.value.toString()
            query["volume"] =
                if (!TextUtils.isEmpty(formulationData.volume)) formulationData.volume.toString()
                    .replace(",", "").toDouble().toString() else "0.0"

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .updatedCompanyData(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<UpdateCompanyResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        makeToast(e.localizedMessage)
                        hideProgress()
                    }

                    override fun onNext(listBean: UpdateCompanyResponse) {
                        hideProgress()
                        if (listBean.status_code == 200) {
                            makeToast(listBean.message)
                            finish()
                        }
                    }
                })
        } else {
            hideProgress()
            makeToast("Not editable no item id received from server.")
        }
    }

    override fun onAllDataCollect(
        category: String,
        volume: String,
        model: CompanyModel,
        position: Int
    ) {
        if (filterCompanyAllData[position].categoryData == null) {
            this.categoryList.find { it.name == category }
                ?.let {
                    val categoryObject: CompanyListResponse.Data = it
                    model.categoryData = categoryObject
                }
            model.volumeData = if (volume.isNotBlank()) volume.toDouble() else 0.0
            totalPercentage = 0.0

            filterCompanyAllData.forEach {
                totalPercentage += it.volumeData
            }
            if (totalPercentage == 100.0) {
                tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.progess_green))
            } else {
                tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.red))
            }
            tvTotalValuePercentage.text =
                "Total " + String.format("%.1f", totalPercentage) + "%"
        } else {
            this.categoryList.find { it.name == category }
                ?.let {
                    val categoryObject: CompanyListResponse.Data = it
                    filterCompanyAllData[position].categoryData = categoryObject
                }
            filterCompanyAllData[position].volumeData =
                if (volume.isNotBlank()) volume.toDouble() else 0.0
            totalPercentage = 0.0

            filterCompanyAllData.forEach {
                totalPercentage += it.volumeData
            }
            if (totalPercentage == 100.0) {
                tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.progess_green))
            } else {
                tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.red))
            }
            tvTotalValuePercentage.text =
                "Total " + String.format("%.1f", totalPercentage) + "%"
        }
    }

    override fun onDeleteClick(position: Int) {
        filterCompanyAllData.remove(filterCompanyAllData[position])
        totalPercentage = 0.0
        filterCompanyAllData.forEach {
            totalPercentage += it.volumeData
        }
        if (totalPercentage == 100.0) {
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.progess_green))
        } else {
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.red))
        }
        tvTotalValuePercentage.text =
            "Total " + String.format("%.1f", totalPercentage) + "%"
        listAdapter.notifyDataSetChanged()
    }

    override fun onEditClick(position: Int) {
        filterCompanyAllData[position].edit = true
        listAdapter.notifyDataSetChanged()
    }

    override fun onAddClick(position: Int) {
        if (totalPercentage <= 100.0) {
            filterCompanyAllData.add(CompanyModel())
            listAdapter.notifyDataSetChanged()
        } else {
            makeToast("Total percentage value cannot exceed 100.")
        }
    }
}