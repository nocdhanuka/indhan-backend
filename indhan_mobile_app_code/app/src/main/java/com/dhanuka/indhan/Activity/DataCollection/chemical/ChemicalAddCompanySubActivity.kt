package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.os.Bundle
import android.os.Handler
import android.text.TextUtils
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.DataCollection.chemical.adapter.CompanyItemListAdapter
import com.dhanuka.indhan.Model.*
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_chemical_add_sub_company_wise.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.HashMap

class ChemicalAddCompanySubActivity : BaseActivity(), View.OnClickListener,
    CompanyItemListAdapter.AddNewItemListAdapter {
    private lateinit var cropApiDataMap: java.util.HashMap<String, Any>
    private lateinit var districtApiMap: java.util.HashMap<String, Any>
    private val companyModel = CompanyRequestModel()
    private val companyAllData = ArrayList<CompanyModel>()
    private lateinit var adapter: CompanyItemListAdapter
    private lateinit var formulationData: FormulationResponse.Data
    private lateinit var classData: ListClassResponse.Data
    private lateinit var preference: Preference
    private var categoryList = ArrayList<CompanyListResponse.Data>()
    var districtId = ""
    var stateId = ""
    var count = 0
    var totalPercentage = 0.0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_add_sub_company_wise)
        companyAllData.add(CompanyModel())
        getCompanyData()
        preference = Preference(this)
        setListener()
        getIntentData()
    }

    private fun getCompanyData() {
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).companyList
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CompanyListResponse>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: CompanyListResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        categoryList.clear()
                        categoryList.addAll(listBean.data)
                        setAdapterItems()
                    }
                }
            })
    }

    private fun setAdapterItems() {
        val categories = ArrayList<String>()
        categoryList.forEach {
            categories.add(it.name)
        }
        val linearLayoutManager =
            LinearLayoutManager(
                this@ChemicalAddCompanySubActivity,
                LinearLayoutManager.VERTICAL,
                false
            )
        rcvCompanyItems.layoutManager = linearLayoutManager
        adapter =
            CompanyItemListAdapter(companyAllData, categories, this@ChemicalAddCompanySubActivity)
        rcvCompanyItems.setHasFixedSize(true)
        rcvCompanyItems.setHasTransientState(true)
        rcvCompanyItems.setItemViewCacheSize(20)
        rcvCompanyItems.adapter = adapter
    }

    private fun getIntentData() {
        tvUserFY.text = Utility.financialYear
        intent?.getSerializableExtra("district_data").let {
            it.also { districtApiMap = it as java.util.HashMap<String, Any> }
        }
        intent?.getSerializableExtra("crop_data").let {
            it.also { cropApiDataMap = it as java.util.HashMap<String, Any> }
        }
        intent?.getStringExtra("district_name")?.let {
            tvDistrictName.text = it
        }
        intent?.getStringExtra("district_id")?.let {
            districtId = it
        }
        intent?.getStringExtra("state_id")?.let {
            stateId = it
        }
        intent?.getStringExtra("class_data")?.let {
            classData =
                Gson().fromJson<ListClassResponse.Data>(it, ListClassResponse.Data::class.java)
            txtChemicalCompanySelectedClass.text = classData.name
        }
        intent?.getStringExtra("formulation_data")?.let {
            formulationData =
                Gson().fromJson<FormulationResponse.Data>(it, FormulationResponse.Data::class.java)
            tvFormulationName.text = formulationData.name
            tvFormulationVolume.text = "${formulationData.volume} Kg / Ltr"
            tvFormulationTotal.text = "Value - Rs.${formulationData.value}"
        }
    }

    private fun setListener() {
        txtSubmitChemicalCompanyWise.setOnClickListener(this)
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtSubmitChemicalCompanyWise -> {
                if (totalPercentage == 100.0) {
                    saveDistrictData()
                    txtSubmitChemicalCompanyWise.isEnabled = false
                    Handler().postDelayed({
                        txtSubmitChemicalCompanyWise.isEnabled = true
                    }, 2400)
                } else {
                    makeToast("Total must be 100%.")
                }
            }
        }
    }

    private fun saveDistrictData() {
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .addChemicalDistrictData(districtApiMap)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ChemicalAddDistrictWiseResponse>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable) {
                    makeToast(e.localizedMessage)
                    hideProgress()
                }

                override fun onNext(data: ChemicalAddDistrictWiseResponse) {
                    if (data.status_code == 200) {
                        saveCropData()
                    } else {
                        makeToast(data.message)
                        hideProgress()
                    }
                }
            })
    }

    private fun saveCropData() {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .addCropData(cropApiDataMap)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AddCropResponse>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable) {
                    makeToast(e.localizedMessage)
                    hideProgress()
                }

                override fun onNext(listBean: AddCropResponse) {
                    if (listBean.status_code == 200) {
                        saveDataCompany()
                    } else {
                        makeToast(listBean.message)
                        hideProgress()
                    }
                }
            })
    }

    private fun saveDataCompany() {

        companyAllData.forEach {
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

        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
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
        query["hindustan_insecticides_ltd"] = companyModel.hindustan_insecticides_ltd
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
        query["volume"] = districtApiMap["volume"] ?: "0"


        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .saveCompanyData(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ChemicalAddCompanyResponse>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable) {
                    makeToast(e.localizedMessage)
                    hideProgress()
                }

                override fun onNext(listBean: ChemicalAddCompanyResponse) {
                    if (listBean.status_code == 200) {
                        makeToast("Data collection updated successfully.")
                        setResult(RESULT_OK)
                        hideProgress()
                        finish()
                    }
                }
            })
    }

    override fun onAllDataCollect(category: String, volume: String, model: CompanyModel) {
        if (volume.isNotBlank()) {
            this.categoryList.find { it.name == category }
                ?.let {
                    val categoryObject: CompanyListResponse.Data = it
                    model.categoryData = categoryObject
                }
            var volumeData = 0.0
            try {
                volumeData = if (volume.isNotBlank()) volume.toDouble() else 0.0
            } catch (e: Exception) {
                e.printStackTrace()
            }
            model.volumeData = volumeData
            totalPercentage = 0.0

            companyAllData.forEach {
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

    override fun onAdd() {
        if (totalPercentage < 100.0) {
            companyAllData.add(CompanyModel())
            adapter.notifyDataSetChanged()
        } else {
            makeToast("Total percentage value cannot exceed 100%.")
        }
    }

}