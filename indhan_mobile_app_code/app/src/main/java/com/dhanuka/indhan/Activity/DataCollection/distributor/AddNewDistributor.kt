package com.dhanuka.indhan.Activity.DataCollection.distributor

import android.os.Bundle
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.Model.AddDistributorResponse
import com.dhanuka.indhan.Model.CompanyList
import com.dhanuka.indhan.Model.DistributorResponse
import com.dhanuka.indhan.Model.UpdateDistributor
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.MultipleModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.MultipleNumberSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.Currency
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.OnSpinnerMultipleItemClick
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_new_distributor.*
import org.json.JSONArray
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class AddNewDistributor : BaseActivity(), View.OnClickListener {
    private val companyList = ArrayList<CompanyList.BrandDataModel>()
    private val brandsIdList = ArrayList<String>()
    private val brandsList = ArrayList<MultipleModel>()
    private val selectedBrandList = ArrayList<String>()
    private val cropsList = ArrayList<MultipleModel>()
    private var ratings: String = ""
    private val cropsIdList = ArrayList<String>()
    private val selectedCropList = ArrayList<String>()
    private var cropList = ArrayList<ProductCropResponse.ProductCropList>()
    private var rating: NumberSpinner? = null
    private var selectCrops: MultipleNumberSpinner? = null
    private var selectBrands: MultipleNumberSpinner? = null
    private var distObject = DistributorResponse.Datum()
    private lateinit var preference: Preference

    private var cropStr: String = ""
    private var brandStr: String = ""
    private var districtId = ""
    private var territoryId = ""
    private var marketId = ""
    private var stateId = ""
    private var cropId = ""
    private var isExist: Boolean = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_new_distributor)
        preference = Preference(this)
        txtSelectCrops.setOnClickListener(this)
        tilBrandWorking.setOnClickListener(this)
        txtSubmitDistributor.setOnClickListener(this)
        txtCropsWorking.setOnClickListener(this)
        getIntentData()
        getCrops()
    }

    private fun getBrands() {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).brands
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CompanyList>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: CompanyList) {
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
        setData(distObject)
        val listener = object : OnSpinnerMultipleItemClick {
            override fun onClick(data: java.util.ArrayList<MultipleModel>) {
                brandsIdList.clear()
                selectedBrandList.clear()
                data.forEach {
                    brandStr = it.name
                    val brandId = it.id
                    brandsIdList.add(brandId)
                    selectedBrandList.add(brandStr)
                    tilBrandWorking.text =
                        selectedBrandList.toString().replace("[", "").replace("]", "")
                }
            }
        }
        selectBrands?.bindOnSpinerListener(listener)
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
                    txtSelectCrops.text =
                        selectedCropList.toString().replace("[", "").replace("]", "")
                }
            }
        }
        selectCrops?.bindOnSpinerListener(listeners)
    }

    private fun getIntentData() {
        rating =
            NumberSpinner(this, Utility.rating, "Select Rating")
        rating?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                ratings = item
                txtCropsWorking.text = item
            }
        })
        intent?.getStringExtra("distributor_data")?.let {
            isExist = true
            distObject = Gson().fromJson(it, DistributorResponse.Datum::class.java)
        }

        if (!isExist) {
//            txtCounterPercentage.isEnabled = true
//            txtCounterPercentage.isFocusable = true
            txtTitle.text = "Add New Channel"
            txtTurnOverDhanukaValue.text = "Revenue by indirect sales of DAL Products if Any?"

        } else {
//            txtCounterPercentage.isEnabled = false
//            txtCounterPercentage.isFocusable = false
            txtTitle.text = "Edit Channel"
            txtTurnOverDhanukaValue.text = "Total turnover with Dhanuka only (LY)"
        }

        intent?.getStringExtra("district_id").let {
            districtId = it.toString()
        }

        intent?.getStringExtra("district_name").let {
            txtDistrictSelected.text = it.toString()
        }

        intent?.getStringExtra("territory_id").let {
            territoryId = it.toString()
        }

        intent?.getStringExtra("state_id").let {
            stateId = it.toString()
        }

        intent?.getStringExtra("territory_name").let {
            txtTerritorySelected.text = it.toString()
        }

        intent?.getStringExtra("market_id").let {
            marketId = it.toString()
        }

        intent?.getStringExtra("market_name").let {
            txtMarketSelected.text = it.toString()
        }
    }

    private fun setData(distributorData: DistributorResponse.Datum?) {
        etDistributorFirmName.setText("${distributorData?.distributerforname ?: ""}")
        etDistributorName.setText("${distributorData?.distributer_name ?: ""}")
        etMobileNumber.setText("${distributorData?.mobilenumber ?: ""}")
        etMobileNumberSecond.setText("${distributorData?.mobile2 ?: ""}")
        etAddress.setText("${distributorData?.adress ?: ""}")
        etPinCode.setText("${distributorData?.pincode ?: ""}")
        etTurnOVer.setText("${distributorData?.turnover ?: ""}")
        txtCounterPercentage.setText("${distributorData?.counter_percentage_pesticides ?: ""}")
        txtTotalPesticideValue.setText("${distributorData?.pesticide_turnover ?: ""}")
        if (!TextUtils.isEmpty(distributorData?.pesticide_turnover ?: "")) {
            txtTurnOverDhanuka.text =
                "In Words - ${
                    Currency.convertToIndianCurrency(
                        distributorData?.pesticide_turnover.toString().trim()
                    )
                }"
        }
        if (!TextUtils.isEmpty(distributorData?.turnover ?: "")) {
            txtValueNumbers.text =
                "In Words - ${
                    Currency.convertToIndianCurrency(
                        distributorData?.turnover.toString().trim()
                    )
                }"
        }

        etTurnOVer.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                if (!TextUtils.isEmpty(s.toString())) {
                    txtValueNumbers.text =
                        "In Words - ${Currency.convertToIndianCurrency(s.toString().trim())}"
                } else {
                    txtValueNumbers.text = "In Words -"
                }
            }

            override fun afterTextChanged(s: Editable?) {

            }

        })

        txtTotalPesticideValue.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                if (!TextUtils.isEmpty(s.toString())) {
                    txtTurnOverDhanuka.text =
                        "In Words - ${Currency.convertToIndianCurrency(s.toString().trim())}"
                } else {
                    txtTurnOverDhanuka.text = "In Words -"
                }
            }

            override fun afterTextChanged(s: Editable?) {

            }

        })

        if (!TextUtils.isEmpty(distributorData?.crope)) {
            val cropArray = JSONArray("${distributorData?.crope ?: "[]"}")
            for (i in 0 until cropArray.length()) {
                cropsIdList.add(cropArray[i].toString())
                val cropsName = cropList.find { it.cropId == cropArray[i] }?.crop
                selectedCropList.add(cropsName.toString())
                txtSelectCrops.text =
                    selectedCropList.toString().replace("[", "").replace("]", "")
            }
        }
        if (!TextUtils.isEmpty(distributorData?.brands)) {
            val brandArray = JSONArray("${distributorData?.brands ?: "[]"}")
            for (i in 0 until brandArray.length()) {
                brandsIdList.add(brandArray[i].toString())
                val brandsName = companyList.find { it.c_id == brandArray[i] }?.name
                selectedBrandList.add(brandsName.toString())
                tilBrandWorking.text =
                    selectedBrandList.toString().replace("[", "").replace("]", "")
            }
        }
        if (!TextUtils.isEmpty(distributorData?.ratings ?: "")) {
            ratings = when {
                distributorData?.ratings!! == "1" -> {
                    "Not upto the mark"
                }
                distributorData.ratings!! == "2" -> {
                    "Average"
                }
                distributorData.ratings!! == "3" -> {
                    "Good"
                }
                distributorData.ratings!! == "4" -> {
                    "Very good"
                }
                distributorData.ratings!! == "5" -> {
                    "Excellent"
                }
                else -> {
                    ""
                }
            }
            txtCropsWorking.text = ratings
        }
        txtAddProperitor.setText((distributorData?.connected_retailers ?: "").toString())
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtSelectCrops -> {
                selectCrops?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog(cropsIdList)
                    }
                }
            }
            R.id.txtSubmitDistributor -> {
                if (validateFields()) {
                    if (isExist) {
                        updateDistributorData()
                    } else {
                        callApiSaveDistributor()
                    }
                }
            }
            R.id.tilBrandWorking -> {
                selectBrands?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog(brandsIdList)
                    }
                }
            }
            R.id.txtCropsWorking -> {
                rating?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }

            }
        }
    }

    private fun updateDistributorData() {
        if (checkDistributor()) {
            showProgress()
            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["territory_id"] = territoryId
            query["district_id"] = districtId
            query["market"] = marketId
            query["state_id"] = stateId
            query["id"] = distObject.id
            query["distributer_name"] = etDistributorName.text.toString().trim()
            query["counter_percentage_pesticides"] = txtCounterPercentage.text.toString().trim()
            query["mobilenumber"] = etMobileNumber.text.toString().trim()
            query["mobile2"] = etMobileNumberSecond.text.toString().trim()
            query["Adress"] = etAddress.text.toString().trim()
            query["pincode"] = etPinCode.text.toString().trim()
            query["turnover"] = etTurnOVer.text.toString().trim()
            query["distributerforname"] = etDistributorFirmName.text.toString().trim()
            val cropJsonArray = JSONArray(cropsIdList)
            val brandJsonArray = JSONArray(brandsIdList)
            query["crope"] = cropJsonArray.toString()
            query["brands"] = brandJsonArray.toString()
            query["connected_retailers"] = txtAddProperitor.text.toString().trim()
            query["pesticide_turnover"] = txtTotalPesticideValue.text.toString().trim()
            query["ratings"] = getRatings(ratings)

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .updateDistributorData(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<UpdateDistributor>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        makeToast(e.localizedMessage)
                        hideProgress()
                    }

                    override fun onNext(listBean: UpdateDistributor) {
                        if (listBean.status_code == 200) {
                            hideProgress()
                            makeToast("Channel Updated Successfully.")
                            setResult(RESULT_OK)
                            finish()
                        }
                    }
                })
        }
    }

    private fun getRatings(rating: String): String {
        when {
            rating.equals("Excellent", ignoreCase = true) -> {
                return "5"
            }
            rating.equals("Very good", ignoreCase = true) -> {
                return "4"
            }
            rating.equals("Good", ignoreCase = true) -> {
                return "3"
            }
            rating.equals("Average", ignoreCase = true) -> {
                return "2"
            }
            rating.equals("Not upto the mark", ignoreCase = true) -> {
                return "1"
            }
            else -> {
                return ""
            }
        }
    }

    private fun checkDistributor(): Boolean {
        return if (!TextUtils.isEmpty(txtTotalPesticideValue.text.toString().trim())) {
            if (!TextUtils.isEmpty(txtAddProperitor.text.toString().trim())) {
                if (ratings.trim().isEmpty() || ratings == "0") {
                    makeToast("please select rating with channel.")
                    false
                } else {
                    true
                }
            } else {
                makeToast("please add number of retailers connected.")
                false
            }
        } else {
            makeToast("Please enter total pesticide turnover.")
            false
        }
    }

    private fun callApiSaveDistributor() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["territory_id"] = territoryId
        query["district_id"] = districtId
        query["market"] = marketId
        query["state_id"] = stateId
        query["distributer_name"] = etDistributorName.text.toString().trim()
        query["counter_percentage_pesticides"] = txtCounterPercentage.text.toString().trim()
        query["mobilenumber"] = etMobileNumber.text.toString().trim()
        query["mobile2"] = etMobileNumberSecond.text.toString().trim()
        query["Adress"] = etAddress.text.toString().trim()
        query["pincode"] = etPinCode.text.toString().trim()
        query["turnover"] = etTurnOVer.text.toString().trim()
        query["distributerforname"] = etDistributorFirmName.text.toString().trim()
        val cropJsonArray = JSONArray(cropsIdList)
        val brandJsonArray = JSONArray(brandsIdList)
        query["crope"] = cropJsonArray.toString()
        query["brands"] = brandJsonArray.toString()
        query["connected_retailers"] = txtAddProperitor.text.toString().trim()
        query["ratings"] = getRatings(ratings)
        query["pesticide_turnover"] = txtTotalPesticideValue.text.toString().trim()

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .saveDistributorData(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AddDistributorResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    makeToast(e.localizedMessage)
                    hideProgress()
                }

                override fun onNext(listBean: AddDistributorResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        makeToast("Channel Saved Successfully.")
                        setResult(RESULT_OK)
                        finish()
                    }
                }
            })
    }

    private fun validateFields(): Boolean {
        if (!TextUtils.isEmpty(etDistributorFirmName.text.toString().trim())) {
            if (!TextUtils.isEmpty(etDistributorName.text.toString().trim())) {
                if (!TextUtils.isEmpty(etMobileNumber.text.toString().trim())) {
                    if (!TextUtils.isEmpty(etAddress.text.toString().trim())) {
                        if (!TextUtils.isEmpty(etPinCode.text.toString().trim())) {
                            if (!TextUtils.isEmpty(etTurnOVer.text.toString().trim())) {
                                return if (!TextUtils.isEmpty(
                                        tilBrandWorking.text.toString().trim()
                                    )
                                ) {
                                    if (!TextUtils.isEmpty(txtSelectCrops.text.toString().trim())) {
                                        if (!TextUtils.isEmpty(
                                                txtTotalPesticideValue.text.toString().trim()
                                            )
                                        ) {
                                            if (!TextUtils.isEmpty(
                                                    txtAddProperitor.text.toString().trim()
                                                )
                                            ) {
                                                if (!TextUtils.isEmpty(
                                                        txtCounterPercentage.text.toString().trim()
                                                    )
                                                ) {
                                                    if (txtCounterPercentage.text.toString()
                                                            .toDouble() <= 100.0
                                                    ) {
                                                        if (ratings.isEmpty() || ratings == "0") {
                                                            makeToast("please select rating with channel.")
                                                            false
                                                        } else {
                                                            true
                                                        }
                                                    } else {
                                                        makeToast("Retail counter % of total pesticide business cannot be more than 100%.")
                                                        false
                                                    }
                                                } else {
                                                    makeToast("Please enter retail counter % of total pesticide business.")
                                                    false
                                                }
                                            } else {
                                                makeToast("please enter number of retailers connected.")
                                                false
                                            }
                                        } else {
                                            makeToast("Please enter total pesticide turnover.")
                                            false
                                        }
                                    } else {
                                        makeToast("Please select crop working with.")
                                        false
                                    }
                                } else {
                                    makeToast("Please select brand working with.")
                                    false
                                }
                            } else {
                                makeToast("Please enter channel turnover.")
                                return false
                            }
                        } else {
                            makeToast("Please enter channel pincode.")
                            return false
                        }
                    } else {
                        makeToast("Please enter address of channel.")
                        return false
                    }
                } else {
                    makeToast("Please enter channel Mobile number.")
                    return false
                }
            } else {
                makeToast("Please enter channel name.")
                return false
            }
        } else {
            makeToast("Please enter channel firm or proprietor name.")
            return false
        }
    }
}