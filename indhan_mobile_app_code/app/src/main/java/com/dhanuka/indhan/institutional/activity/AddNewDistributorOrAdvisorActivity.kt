package com.dhanuka.indhan.institutional.activity

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.Model.*
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.MultipleModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.MultipleNumberSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.OnSpinnerMultipleItemClick
import kotlinx.android.synthetic.main.activity_add_distributor_advisor.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class AddNewDistributorOrAdvisorActivity : BaseActivity() {

    private var appdataBase: AppDatabase? = null
    private lateinit var preference: Preference

    private var districtList = ArrayList<DistrictResponse.Data>()
    private var territoryList = ArrayList<TerritoryResponse.TerritoryData>()
    private var marketList = ArrayList<MarketResponse.Data>()
    private var territory = java.util.ArrayList<String>()
    private var market = java.util.ArrayList<String>()
    private val companyList = ArrayList<CompanyListResponse.Data>()
    private var cropList = ArrayList<ProductCropResponse.ProductCropList>()
    private val brandsIdList = ArrayList<String>()
    private val brandsList = ArrayList<MultipleModel>()
    private val selectedBrandList = ArrayList<String>()
    private val cropsList = ArrayList<MultipleModel>()
    private val cropsIdList = ArrayList<String>()
    private val selectedCropList = ArrayList<String>()

    private var selectCrops: MultipleNumberSpinner? = null
    private var selectBrands: MultipleNumberSpinner? = null

    private var selectDistrict: NumberSpinner? = null
    private var selectTerritory: NumberSpinner? = null
    private var selectMarket: NumberSpinner? = null
    private var districtName: String = ""
    private var districtStr: String = ""
    private var territoryStr: String = ""
    private var marketStr: String = ""
    private var cropStr: String = ""
    private var brandStr: String = ""
    private var districtId: String = ""
    private var territoryId: String = ""
    private var marketId: String = ""
    private var stateId: String = ""
    private var cropId: String = ""

    private var isDistributor: Boolean = true

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_distributor_advisor)
        preference = Preference(this)
        appdataBase = DatabaseClient.getInstance(this).appDatabase
        txtName.text = "FL - ".plus(preference.getString("Username"))
        setListeners()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getDistricts()
        } else {
            makeToast("No internet, Please check your internet.")
        }
    }

    private fun setListeners() {
        txtDistrict.setOnClickListener(this)
        txtTerritory.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        txtMarket.setOnClickListener(this)
        txtBrandWorking.setOnClickListener(this)
        txtCropsWorking.setOnClickListener(this)
        txtSubmitDistributor.setOnClickListener(this)
        rbDistributor.isChecked = true
        rgbForWhat.setOnCheckedChangeListener { _, checkedId ->
            when (checkedId) {
                R.id.rbDistributor -> {
                    isDistributor = true
                }
                R.id.rbAdvisor -> {
                    isDistributor = false
                }
            }
        }
    }

    private fun getDistricts() {
        showProgress()
        val stateId = preference.getInt("state_id").toString()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getDistricts(stateId)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DistrictResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: DistrictResponse) {

                    if (listBean.status_code == 200) {
                        districtList.clear()
                        districtList.addAll(listBean.data)
                        setDistrictData()
                    } else {
                        if (listBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                        }
                        hideProgress()
                    }
                }
            })
    }

    private fun setDistrictData() {
        getCrops()
        val districts = ArrayList<String>()
        districtList.forEach {
            districts.add(it.district)
        }
        selectDistrict =
            NumberSpinner(this, districts, "Search or Select District")
        selectDistrict?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                districtStr = item
                txtDistrict.text = item

                val districtObject: DistrictResponse.Data =
                    districtList.find { it.district == districtStr }!!

                districtName = districtObject.district
                districtId = districtObject.district_id.toString()
                stateId = districtObject.state_id.toString()
                marketId = ""
                territoryId = ""
                txtTerritory.text = ""
                txtMarket.text = ""
                getTerritory(districtId.toInt())
            }
        })
    }

    private fun getTerritory(districtId: Int) {
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getAllTerritory(districtId.toString())
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TerritoryResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: TerritoryResponse) {
                    hideProgress()
                    territoryList.clear()
                    territory.clear()
                    if (listBean.status_code == 200) {
                        territoryList.addAll(listBean.data)
                        setTerritoryData()
                    }
                }
            })
    }

    private fun setTerritoryData() {
        territoryList.forEach {
            territory.add(it.territory)
        }
        selectTerritory =
            NumberSpinner(this, territory, "Search or Select Territory")
        selectTerritory?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                market.clear()
                territoryStr = item
                txtTerritory.text = item
                txtMarket.text = ""
                marketId = ""
                val territoryObject: TerritoryResponse.TerritoryData =
                    territoryList.find { it.territory == territoryStr }!!
                territoryId = (territoryObject.territory_id).toString()
                getMarketData(territoryObject.territory_id)
            }
        })
    }

    private fun getMarketData(territoryId: Int) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["territory_id"] = territoryId
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getAllMarketList(territoryId.toString())
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MarketResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: MarketResponse) {
                    hideProgress()
                    marketList.clear()
                    market.clear()
                    if (listBean.status_code == 200) {
                        marketList.addAll(listBean.data)
                        setMarketData("1")
                    }
                }
            })
    }

    private fun setMarketData(type: String) {
        market.clear()
        if (marketList.size > 0) {
            marketList.forEach {
                market.add(it.market)
            }
            selectMarket =
                NumberSpinner(this, market, "Search or Select Market")
            selectMarket?.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    marketStr = item
                    txtMarket.text = item
                    marketId = marketList[position].market_id.toString()
                }
            })
        } else {
            makeToast("No market found please add new market.")
        }
    }

    private fun getCrops() {
        showProgress()
        val apiToken = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).prodCrop(apiToken)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductCropResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ProductCropResponse) {
                    hideProgress()
                    if (listBean.statusCode == 200) {
                        cropList.clear()
                        cropsIdList.clear()
                        cropsList.clear()
                        listBean.data.productCropList.forEach {
                            if (cropsList.find { d -> d.name == it.crop } == null) {
                                val model = MultipleModel()
                                model.name = it.crop
                                model.id = it.cropId
                                model.selected = false
                                cropsList.add(model)
                            }
                        }
                        cropList.addAll(listBean.data.productCropList)
                        setCropData()
                    }
                }
            })
    }

    private fun setCropData() {
        selectCrops =
            MultipleNumberSpinner(this, cropsList, "Select Crops")
        getBrands()
        val listeners = object : OnSpinnerMultipleItemClick {
            override fun onClick(data: java.util.ArrayList<MultipleModel>) {
                cropsIdList.clear()
                selectedCropList.clear()
                data.forEach {
                    cropStr = it.name
                    cropId = it.id
                    cropsIdList.add(cropId)
                    selectedCropList.add(cropStr)
                    txtCropsWorking.text =
                        selectedCropList.toString().replace("[", "").replace("]", "")
                }
            }
        }
        selectCrops?.bindOnSpinerListener(listeners)
    }

    private fun getBrands() {
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
                        companyList.clear()
                        companyList.addAll(listBean.data)
                        setBrandsData()
                    }
                }
            })
    }

    private fun setBrandsData() {
        companyList.forEach {
            val model = MultipleModel()
            model.name = it.name
            model.id = it.c_id
            model.selected = false
            brandsList.add(model)
        }
        selectBrands =
            MultipleNumberSpinner(this, brandsList, "Select Brands")
        val listener = object : OnSpinnerMultipleItemClick {
            override fun onClick(data: java.util.ArrayList<MultipleModel>) {
                brandsIdList.clear()
                selectedBrandList.clear()
                data.forEach {
                    brandStr = it.name
                    val brandId = it.id
                    brandsIdList.add(brandId)
                    selectedBrandList.add(brandStr)
                    txtBrandWorking.text =
                        selectedBrandList.toString().replace("[", "").replace("]", "")
                }
            }
        }
        selectBrands?.bindOnSpinerListener(listener)
    }

    private fun callApiAddNewDistributor() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            showProgress()
            val query = java.util.HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["state_id"] = stateId
            query["district_id"] = districtId
            query["territory_id"] = territoryId
            query["market_id"] = marketId
            query["distributor_name"] = txtADistributorName.text.toString().trim()
            query["distributor_type"] = if (isDistributor) "0" else "1"
            query["distributor_firm_name"] = txtAddProperitor.text.toString().trim()
            query["mobile"] = txtMobileFirst.text.toString().trim()
            query["mobile2"] = txtMobileSecond.text.toString().trim()
            query["address"] = txtAddress.text.toString().trim()
            query["pincode"] = txtPincode.text.toString().trim()
            query["turnover"] = txtTurnOver.text.toString().trim()
            query["crope"] = txtCropsWorking.text.toString().trim()
            query["brands"] = txtBrandWorking.text.toString().trim()
            query["is_distributor"] = if (isDistributor) "0" else "1"
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).addDistributor(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<ArrivalDepartureResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        hideProgress()
                    }

                    override fun onNext(listBean: ArrivalDepartureResponse) {
                        if (listBean.status_code == 200) {
                            makeToast(listBean.message ?: "Distributor added successfully.")
                            finish()
                        } else {
                            if (listBean.status_code == 409) {
                                EventBus.getDefault().post("logout")
                            }
                        }
                        hideProgress()
                    }
                })
        } else {
            makeToast("No internet, Please check your internet.")
        }
    }

    private fun validData(): Boolean {
        if (TextUtils.isEmpty(txtDistrict.text.trim().trim())) {
            makeToast("Please select district.")
            return false
        }
        if (TextUtils.isEmpty(txtTerritory.text.toString().trim())) {
            makeToast("Please select territory.")
            return false
        }
        if (TextUtils.isEmpty(txtMarket.text.toString().trim())) {
            makeToast("Please select market.")
            return false
        }
        if (TextUtils.isEmpty(txtAddProperitor.text.toString().trim())) {
            makeToast("Please enter distributor firm or proprietor name.")
            return false
        }
        if (TextUtils.isEmpty(txtMobileFirst.text.toString().trim())) {
            makeToast("Please enter WhatsApp number.")
            return false
        }
        if (TextUtils.isEmpty(txtAddress.text.toString().trim())) {
            makeToast("Please enter address.")
            return false
        }
        if (TextUtils.isEmpty(txtPincode.text.toString().trim())) {
            makeToast("Please enter pin code.")
            return false
        }
        if (TextUtils.isEmpty(txtTurnOver.text.toString().trim())) {
            makeToast("Please enter turnover.")
            return false
        }
        if (TextUtils.isEmpty(txtBrandWorking.text.toString().trim())) {
            makeToast("Please select brand(s) working with.")
            return false
        }
        if (TextUtils.isEmpty(txtCropsWorking.text.toString().trim())) {
            makeToast("Please select crop(s) working with.")
            return false
        }
        return true
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtDistrict -> {
                if (districtList.size > 0) {
                    selectDistrict?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }
                } else {
                    makeToast("No district found.")
                }
            }
            R.id.ivBack -> {
                finish()
            }
            R.id.txtTerritory -> {
                selectTerritory?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtMarket -> {
                selectMarket?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtBrandWorking -> {
                selectBrands?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog(brandsIdList)
                    }
                }
            }
            R.id.txtCropsWorking -> {
                selectCrops?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog(cropsIdList)
                    }
                }
            }
            R.id.txtSubmitDistributor -> {
                if (validData()) {
                    callApiAddNewDistributor()
                }
            }
        }
    }
}