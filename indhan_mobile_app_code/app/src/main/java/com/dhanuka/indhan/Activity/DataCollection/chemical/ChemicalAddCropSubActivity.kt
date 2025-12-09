package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.content.Intent
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.Constants
import com.dhanuka.indhan.Activity.DataCollection.chemical.adapter.CropItemListAdapter
import com.dhanuka.indhan.Model.*
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_chemical_add_crop_wise.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ChemicalAddCropSubActivity : BaseActivity(), View.OnClickListener,
    CropItemListAdapter.AddNewItemListAdapter {
    private var cropModel = CropRequestModel()
    private lateinit var adapter: CropItemListAdapter
    private var subCategory = ArrayList<FoodSubCategoryResponse.Data>()
    private lateinit var formulationData: FormulationResponse.Data
    private lateinit var classData: ListClassResponse.Data
    private lateinit var preference: Preference
    private var categoryList = ArrayList<FoodCategoryResponse.Data>()
    private var cropAllData = ArrayList<CropModel>()
    var districtId = ""
    var stateId = ""
    var count = 0
    var totalPercentage = 0.0
    var districtApiMap = HashMap<String, Any>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_add_crop_wise)
        cropAllData.add(CropModel())
        getFoodCategory()
        preference = Preference(this)
        setListener()
        getIntentData()
    }

    private fun getFoodCategory() {
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).foodCategory
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<FoodCategoryResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: FoodCategoryResponse) {
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
                this@ChemicalAddCropSubActivity,
                LinearLayoutManager.VERTICAL,
                false
            )
        rcvCropItems.layoutManager = linearLayoutManager
        adapter = CropItemListAdapter(cropAllData, categories, this@ChemicalAddCropSubActivity)
        rcvCropItems.adapter = adapter
        rcvCropItems.setHasFixedSize(true)
    }

    private fun getIntentData() {
        tvUserFY.text = Utility.financialYear
        intent?.getStringExtra("district_id")?.let {
            districtId = it
        }
        intent?.getStringExtra("district_name")?.let {
            tvDistrictName.text = it
        }
        intent?.getStringExtra("state_id")?.let {
            stateId = it
        }
        intent?.getSerializableExtra("add_district_data").let {
            it.also { districtApiMap = it as HashMap<String, Any> }
        }
        intent?.getStringExtra("class_data")?.let {
            classData =
                Gson().fromJson<ListClassResponse.Data>(it, ListClassResponse.Data::class.java)
            txtChemicalCropSelectedClass.text = classData.name
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
        txtSubmitChemicalCropWise.setOnClickListener(this)
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtSubmitChemicalCropWise -> {
                saveDataAddCrop()
            }
        }
    }

    private fun saveDataAddCrop() {
        if (totalPercentage == 100.0) {
            cropAllData.forEach {
                if (it.subcategoryData != null)
                    if (!TextUtils.isEmpty(it.subcategoryData)) {
                        when {
                            it.subcategoryData.trim()
                                .equals("Cereal - Maize", ignoreCase = true) -> {
                                cropModel.maize = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Rice", ignoreCase = true) -> {
                                cropModel.rice = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Wheat", ignoreCase = true) -> {
                                cropModel.wheat = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Cotton", ignoreCase = true) -> {
                                cropModel.cotton = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Grapes", ignoreCase = true) -> {
                                cropModel.grapes = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Apple", ignoreCase = true) -> {
                                cropModel.apple = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Mango", ignoreCase = true) -> {
                                cropModel.mango = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Pomegranate", ignoreCase = true) -> {
                                cropModel.pomegranate = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Citrus", ignoreCase = true) -> {
                                cropModel.citrus = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Other Fruits", ignoreCase = true) -> {
                                cropModel.other_fruits = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Groundnut", ignoreCase = true) -> {
                                cropModel.groundnut = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Soybean", ignoreCase = true) -> {
                                cropModel.soybean = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Other Oilseeds", ignoreCase = true) -> {
                                cropModel.other_oilseeds = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Sugarcane", ignoreCase = true) -> {
                                cropModel.sugarcane = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Tea", ignoreCase = true) -> {
                                cropModel.tea = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Bengal gram", ignoreCase = true) -> {
                                cropModel.bengal_gram = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Black Gram", ignoreCase = true) -> {
                                cropModel.black_gram = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Other pulses", ignoreCase = true) -> {
                                cropModel.other_pulses = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Cumin", ignoreCase = true) -> {
                                cropModel.cumin = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Brinjal", ignoreCase = true) -> {
                                cropModel.brinjal = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Chillies", ignoreCase = true) -> {
                                cropModel.chillies = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Cole crops", ignoreCase = true) -> {
                                cropModel.cole_crops = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Cucurbits/Melons", ignoreCase = true) -> {
                                cropModel.cucurbits_melons = it.volumeData.toString()
                            }
                            it.subcategoryData.trim()
                                .equals("Onion/Garlic", ignoreCase = true) -> {
                                cropModel.onion_garlic = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Potato", ignoreCase = true) -> {
                                cropModel.potato = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Tomato", ignoreCase = true) -> {
                                cropModel.tomato = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Other Veg", ignoreCase = true) -> {
                                cropModel.other_veg = it.volumeData.toString()
                            }
                            it.subcategoryData.trim().equals("Others", ignoreCase = true) -> {
                                cropModel.others = it.volumeData.toString()
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
            query["maize"] = cropModel.maize
            query["rice"] = cropModel.rice
            query["wheat"] = cropModel.wheat
            query["cotton"] = cropModel.cotton
            query["grapes"] = cropModel.grapes
            query["apple"] = cropModel.apple
            query["mango"] = cropModel.mango
            query["pomegranate"] = cropModel.pomegranate
            query["citrus"] = cropModel.citrus
            query["other_fruits"] = cropModel.other_fruits
            query["groundnut"] = cropModel.groundnut
            query["soybean"] = cropModel.soybean
            query["other_oilseeds"] = cropModel.other_oilseeds
            query["sugarcane"] = cropModel.sugarcane
            query["tea"] = cropModel.tea
            query["bengal_gram"] = cropModel.bengal_gram
            query["black_gram"] = cropModel.black_gram
            query["other_pulses"] = cropModel.other_pulses
            query["cumin"] = cropModel.cumin
            query["brinjal"] = cropModel.brinjal
            query["chillies"] = cropModel.chillies
            query["cole_crops"] = cropModel.cole_crops
            query["cucurbits_melons"] = cropModel.cucurbits_melons
            query["onion_garlic"] = cropModel.onion_garlic
            query["potato"] = cropModel.potato
            query["tomato"] = cropModel.tomato
            query["other_veg"] = cropModel.other_veg
            query["others"] = cropModel.others
            query["total"] = totalPercentage.toString()
            query["percentage"] = totalPercentage.toString()
            query["volume"] = districtApiMap["volume"] ?: "0"
            val bundle = Bundle()
            bundle.putString("district_id", districtId)
            bundle.putString("state_id", stateId)
            bundle.putString("class_data", Gson().toJson(classData))
            bundle.putSerializable("district_data", districtApiMap)
            bundle.putSerializable("district_name", tvDistrictName.text.toString())
            bundle.putSerializable("crop_data", query)
            bundle.putString("formulation_data", Gson().toJson(formulationData))
            val intent = Intent(activity, ChemicalAddCompanySubActivity::class.java)
            intent.putExtras(bundle)
            startActivityForResult(intent, Constants.ACTIVITY_RESULT_COMPANY)

        } else {
            makeToast("Total must be 100 %.")
        }

    }

    override fun onCategorySelected(category: String, position: Int) {
        val categoryObject: FoodCategoryResponse.Data =
            categoryList.find { it.name == category }!!
        getCropsData(categoryObject.fc_id, position)
    }

    override fun onAddRow(position: Int) {
        if (totalPercentage < 100.0) {
            cropAllData.add(CropModel())
            adapter.notifyDataSetChanged()
        } else {
            makeToast("Total percentage value cannot exceed 100.")
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == Constants.ACTIVITY_RESULT_COMPANY) {
                setResult(RESULT_OK)
                finish()
            }
        }
    }

    override fun onAllDataCollect(
        category: String,
        subCategory: String,
        volume: String,
        cropModel: CropModel,
        position: Int
    ) {
        this.subCategory.find { it.name == subCategory }?.let {
            val subCategoryObject: FoodSubCategoryResponse.Data = it
            cropAllData[position].subcategoryData = subCategoryObject.name
        }

        this.categoryList.find { it.name == category }
            ?.let {
                val categoryObject: FoodCategoryResponse.Data = it
                cropAllData[position].categoryData = categoryObject
            }
        var volumeData = 0.0
        try {
            volumeData = if (volume.isNotBlank()) volume.toDouble() else 0.0
        } catch (e: Exception) {
            e.printStackTrace()
        }
        cropAllData[position].volumeData = volumeData

        totalPercentage = 0.0

        cropAllData.forEach {
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

    private fun getCropsData(fcId: String?, position: Int) {
        showProgress()
        val query = HashMap<String, Any>()
        query["foodcategory_id"] = fcId.toString()
        query["apiToken"] = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getFoodSubCategory(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<FoodSubCategoryResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: FoodSubCategoryResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        subCategory.clear()
                        subCategory.addAll(listBean.data)
                        setSubCategoryData(position)
                    }
                }
            })
    }

    private fun setSubCategoryData(position: Int) {
        val subCategories = ArrayList<String>()
        subCategory.forEach {
            subCategories.add(it.name)
        }
        adapter.setCropData(subCategories)
        adapter.notifyDataSetChanged()
    }
}