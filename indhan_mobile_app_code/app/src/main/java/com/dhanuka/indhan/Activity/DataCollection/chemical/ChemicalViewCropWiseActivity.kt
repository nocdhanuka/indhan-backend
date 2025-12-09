package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.os.Bundle
import android.text.TextUtils
import android.view.MenuInflater
import android.view.MenuItem
import android.view.View
import android.widget.PopupMenu
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.DataCollection.chemical.adapter.ChemicalCropWiseItemAdapter
import com.dhanuka.indhan.Model.*
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_chemical_view_crop_wise.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ChemicalViewCropWiseActivity : BaseActivity(), View.OnClickListener,
    ChemicalCropWiseItemAdapter.AddNewItemListAdapter,
    PopupMenu.OnMenuItemClickListener {

    private var index: Int = 0
    private var formulationStr: String = ""
    private val cropModel = CropRequestModel()
    private var subCategory = ArrayList<FoodSubCategoryResponse.Data>()
    private lateinit var preference: Preference
    private var chemicalDataCropWise = ArrayList<ChemicalCropWiseListResponse.Data>()
    private var categoryList = ArrayList<FoodCategoryResponse.Data>()
    private lateinit var listAdapter: ChemicalCropWiseItemAdapter
    private val cropAllData = ArrayList<CropModel>()
    private val filterCropAllData = ArrayList<CropModel>()
    private lateinit var formulationData: FormulationResponse.Data
    private lateinit var classData: ListClassResponse.Data
    private var totalPercentage = 0.0
    private var districtName = ""
    var districtId = ""
    var stateId = ""
    private var listFormulation = ArrayList<String>()
    private var selectFormulation: NumberSpinner? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_view_crop_wise)
        preference = Preference(this)
        txtSubmitChemicalCropWise.setOnClickListener(this)
        imgFilter.setOnClickListener(this)
        txtFilterChemical.setOnClickListener(this)
        getIntentData()
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
        getChemicalCropWise()
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.imgFilter -> {
                showPopup(v)
            }
            R.id.txtSubmitChemicalCropWise -> {
                updateCropWiseData()
            }
            R.id.txtFilterChemical -> {
                selectFormulation?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }

            }
        }
    }

    fun showPopup(v: View?) {
        val popup = PopupMenu(this, v)
        val inflater: MenuInflater = popup.menuInflater
        popup.setOnMenuItemClickListener(this);
        inflater.inflate(R.menu.menu, popup.menu)
        popup.show()
    }

    override fun onMenuItemClick(item: MenuItem): Boolean {
        return when (item.itemId) {
            R.id.company -> {
                val bundle = Bundle();
                bundle.putString("district_id", districtId)
                bundle.putString("state_id", stateId)
                bundle.putString("district_name", districtName)
                launchIntent(ChemicalViewCompanyWiseActivity::class.java, bundle, true)
                true
            }
            R.id.crop -> {
                false
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

    private fun getChemicalCropWise() {
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        showProgress()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .chemicalCropWiseRecord(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ChemicalCropWiseListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ChemicalCropWiseListResponse) {
                    if (listBean.status_code == 200) {
                        chemicalDataCropWise.clear()
                        if (listBean.data.size > 0) {
                            listBean.data.forEach {
                                if (it.district.equals(districtName, ignoreCase = true)) {
                                    chemicalDataCropWise.add(it)
                                    listFormulation.add(it.formulationname)
                                }
                            }
                        }
                        if (chemicalDataCropWise.size > 0) {
                            selectFormulation =
                                NumberSpinner(
                                    this@ChemicalViewCropWiseActivity,
                                    listFormulation,
                                    "Search or select formulation"
                                )
                            selectFormulation?.bindOnSpinerListener(object : OnSpinerItemClick {
                                override fun onClick(item: String, position: Int) {
                                    formulationStr = item
                                    txtFilterChemical.text = item
                                    for ((pos) in chemicalDataCropWise.withIndex()) {
                                        if (chemicalDataCropWise[pos].formulationname.equals(
                                                item,
                                                ignoreCase = true
                                            )
                                        ) {
                                            index = pos
                                            setAdapterItems()
                                            txtFilterChemical.text = chemicalDataCropWise[pos].formulationname.toString()
//                                            getClassData(
//                                                chemicalDataCropWise[pos].classname.toString(),
//                                                chemicalDataCropWise[pos].formulationname.toString()
//                                            )
                                            break
                                        }
                                    }
                                }
                            })
                            txtFilterChemical.text = chemicalDataCropWise[index].formulationname.toString()
                            getFoodCategory()
//                            getClassList(chemicalDataCropWise[index])
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

    private fun getClassList(data: ChemicalCropWiseListResponse.Data) {
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
                    if (listBean.status_code == 200) {
                        if (listBean.data.size > 0) {
                            for ((index) in listBean.data.withIndex()) {
                                if (listBean.data[index].name.replace(" ","").equals(
                                        formulationName.replace(" ",""),
                                        ignoreCase = true
                                    )
                                ) {
                                    formulationData = listBean.data[index]
                                    getFoodCategory()
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
        showProgress()
        cropAllData.clear()
        val categories = ArrayList<String>()
        categoryList.forEach {
            categories.add(it.name)
        }

        val cropModelMaize = CropModel()
        cropModelMaize.categoryData =
            categoryList.find { it.name.equals("Cereals\t\t", ignoreCase = true) }
        cropModelMaize.subcategoryData = "Cereal - Maize"
        cropModelMaize.volumeData =
            if (chemicalDataCropWise[index].maize != null && chemicalDataCropWise[index].maize.isNotEmpty()) chemicalDataCropWise[index].maize.toDouble() else 0.0
        cropAllData.add(cropModelMaize)

        val cropModelRice = CropModel()
        cropModelRice.categoryData =
            categoryList.find { it.name.equals("Cereals\t\t", ignoreCase = true) }
        cropModelRice.subcategoryData = "Rice"
        cropModelRice.volumeData =
            if (chemicalDataCropWise[index].rice != null && chemicalDataCropWise[index].rice.isNotEmpty()) chemicalDataCropWise[index].rice.toDouble() else 0.0
        cropAllData.add(cropModelRice)

        val cropModelWheat = CropModel()
        cropModelWheat.categoryData =
            categoryList.find { it.name.equals("Cereals\t\t", ignoreCase = true) }
        cropModelWheat.subcategoryData = "Wheat"
        cropModelWheat.volumeData =
            if (chemicalDataCropWise[index].wheat != null && chemicalDataCropWise[index].wheat.isNotEmpty()) chemicalDataCropWise[index].wheat.toDouble() else 0.0
        cropAllData.add(cropModelWheat)

        val cropModelCotton = CropModel()
        cropModelCotton.categoryData =
            categoryList.find { it.name.equals("Fibres", ignoreCase = true) }
        cropModelCotton.subcategoryData = "Cotton"
        cropModelCotton.volumeData =
            if (chemicalDataCropWise[index].cotton != null && chemicalDataCropWise[index].cotton.isNotEmpty()) chemicalDataCropWise[index].cotton.toDouble() else 0.0
        cropAllData.add(cropModelCotton)

        val cropModelGrapes = CropModel()
        cropModelGrapes.categoryData =
            categoryList.find { it.name.equals("Fruits", ignoreCase = true) }
        cropModelGrapes.subcategoryData = "Grapes"
        cropModelGrapes.volumeData =
            if (chemicalDataCropWise[index].grapes != null && chemicalDataCropWise[index].grapes.isNotEmpty()) chemicalDataCropWise[index].grapes.toDouble() else 0.0
        cropAllData.add(cropModelGrapes)

        val cropModelApple = CropModel()
        cropModelApple.categoryData =
            categoryList.find { it.name.equals("Fruits", ignoreCase = true) }
        cropModelApple.subcategoryData = "Apple"
        cropModelApple.volumeData =
            if (chemicalDataCropWise[index].apple != null && chemicalDataCropWise[index].apple.isNotEmpty()) chemicalDataCropWise[index].apple.toDouble() else 0.0
        cropAllData.add(cropModelApple)

        val cropModelMango = CropModel()
        cropModelMango.categoryData =
            categoryList.find { it.name.equals("Fruits", ignoreCase = true) }
        cropModelMango.subcategoryData = "Mango"
        cropModelMango.volumeData =
            if (chemicalDataCropWise[index].mango != null && chemicalDataCropWise[index].mango.isNotEmpty()) chemicalDataCropWise[index].mango.toDouble() else 0.0
        cropAllData.add(cropModelMango)

        val cropModelPomegranate = CropModel()
        cropModelPomegranate.categoryData =
            categoryList.find { it.name.equals("Fruits", ignoreCase = true) }
        cropModelPomegranate.subcategoryData = "Pomegranate"
        cropModelPomegranate.volumeData =
            if (chemicalDataCropWise[index].pomegranate != null && chemicalDataCropWise[index].pomegranate.isNotEmpty()) chemicalDataCropWise[index].pomegranate.toDouble() else 0.0
        cropAllData.add(cropModelPomegranate)

        val cropModelCitrus = CropModel()
        cropModelCitrus.categoryData =
            categoryList.find { it.name.equals("Fruits", ignoreCase = true) }
        cropModelCitrus.subcategoryData = "Citrus"
        cropModelCitrus.volumeData =
            if (chemicalDataCropWise[index].citrus != null && chemicalDataCropWise[index].citrus.isNotEmpty()) chemicalDataCropWise[index].citrus.toDouble() else 0.0
        cropAllData.add(cropModelCitrus)

        val cropModelOtherFruits = CropModel()
        cropModelOtherFruits.categoryData =
            categoryList.find { it.name.equals("Fruits", ignoreCase = true) }
        cropModelOtherFruits.subcategoryData = "Other Fruits"
        cropModelOtherFruits.volumeData =
            if (chemicalDataCropWise[index].other_fruits != null && chemicalDataCropWise[index].other_fruits.isNotEmpty()) chemicalDataCropWise[index].other_fruits.toDouble() else 0.0
        cropAllData.add(cropModelOtherFruits)

        val cropModelGroundnut = CropModel()
        cropModelGroundnut.categoryData =
            categoryList.find { it.name.equals("Oilseeds", ignoreCase = true) }
        cropModelGroundnut.subcategoryData = "Groundnut"
        cropModelGroundnut.volumeData =
            if (chemicalDataCropWise[index].groundnut != null && chemicalDataCropWise[index].groundnut.isNotEmpty()) chemicalDataCropWise[index].groundnut.toDouble() else 0.0
        cropAllData.add(cropModelGroundnut)

        val cropModelSoybean = CropModel()
        cropModelSoybean.categoryData =
            categoryList.find { it.name.equals("Oilseeds", ignoreCase = true) }
        cropModelSoybean.subcategoryData = "Soybean"
        cropModelSoybean.volumeData =
            if (chemicalDataCropWise[index].soybean != null && chemicalDataCropWise[index].soybean.isNotEmpty()) chemicalDataCropWise[index].soybean.toDouble() else 0.0
        cropAllData.add(cropModelSoybean)

        val cropModelOtherOilSeeds = CropModel()
        cropModelOtherOilSeeds.categoryData =
            categoryList.find { it.name.equals("Oilseeds", ignoreCase = true) }
        cropModelOtherOilSeeds.subcategoryData = "Other Oilseeds"
        cropModelOtherOilSeeds.volumeData =
            if (chemicalDataCropWise[index].other_oilseeds != null && chemicalDataCropWise[index].other_oilseeds.isNotEmpty()) chemicalDataCropWise[index].other_oilseeds.toDouble() else 0.0
        cropAllData.add(cropModelOtherOilSeeds)

        val cropModelSugarcane = CropModel()
        cropModelSugarcane.categoryData =
            categoryList.find { it.name.equals("Plantations", ignoreCase = true) }
        cropModelSugarcane.subcategoryData = "Sugarcane"
        cropModelSugarcane.volumeData =
            if (chemicalDataCropWise[index].sugarcane != null && chemicalDataCropWise[index].sugarcane.isNotEmpty()) chemicalDataCropWise[index].sugarcane.toDouble() else 0.0
        cropAllData.add(cropModelSugarcane)

        val cropModelTea = CropModel()
        cropModelTea.categoryData =
            categoryList.find { it.name.equals("Plantations", ignoreCase = true) }
        cropModelTea.subcategoryData = "Tea"
        cropModelTea.volumeData =
            if (chemicalDataCropWise[index].tea != null && chemicalDataCropWise[index].tea.isNotEmpty()) chemicalDataCropWise[index].tea.toDouble() else 0.0
        cropAllData.add(cropModelTea)

        val cropModelBengalGram = CropModel()
        cropModelBengalGram.categoryData =
            categoryList.find { it.name.equals("Pulses", ignoreCase = true) }
        cropModelBengalGram.subcategoryData = "Bengal gram"
        cropModelBengalGram.volumeData =
            if (chemicalDataCropWise[index].bengal_gram != null && chemicalDataCropWise[index].bengal_gram.isNotEmpty()) chemicalDataCropWise[index].bengal_gram.toDouble() else 0.0
        cropAllData.add(cropModelBengalGram)

        val cropModelBlackGram = CropModel()
        cropModelBlackGram.categoryData =
            categoryList.find { it.name.equals("Pulses", ignoreCase = true) }
        cropModelBlackGram.subcategoryData = "Black Gram"
        cropModelBlackGram.volumeData =
            if (chemicalDataCropWise[index].black_gram != null && chemicalDataCropWise[index].black_gram.isNotEmpty()) chemicalDataCropWise[index].black_gram.toDouble() else 0.0
        cropAllData.add(cropModelBlackGram)

        val cropModelOtherPulses = CropModel()
        cropModelOtherPulses.categoryData =
            categoryList.find { it.name.equals("Pulses", ignoreCase = true) }
        cropModelOtherPulses.subcategoryData = "Other pulses"
        cropModelOtherPulses.volumeData =
            if (chemicalDataCropWise[index].other_pulses != null && chemicalDataCropWise[index].other_pulses.isNotEmpty()) chemicalDataCropWise[index].other_pulses.toDouble() else 0.0
        cropAllData.add(cropModelOtherPulses)

        val cropModelCumin = CropModel()
        cropModelCumin.categoryData =
            categoryList.find { it.name.equals("Spices", ignoreCase = true) }
        cropModelCumin.subcategoryData = "Cumin"
        cropModelCumin.volumeData =
            if (chemicalDataCropWise[index].cumin != null && chemicalDataCropWise[index].cumin.isNotEmpty()) chemicalDataCropWise[index].cumin.toDouble() else 0.0
        cropAllData.add(cropModelCumin)

        val cropModelBrinjal = CropModel()
        cropModelBrinjal.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelBrinjal.subcategoryData = "Brinjal"
        cropModelBrinjal.volumeData =
            if (chemicalDataCropWise[index].brinjal != null && chemicalDataCropWise[index].brinjal.isNotEmpty()) chemicalDataCropWise[index].brinjal.toDouble() else 0.0
        cropAllData.add(cropModelBrinjal)

        val cropModelChillies = CropModel()
        cropModelChillies.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelChillies.subcategoryData = "Chillies"
        cropModelChillies.volumeData =
            if (chemicalDataCropWise[index].chillies != null && chemicalDataCropWise[index].chillies.isNotEmpty()) chemicalDataCropWise[index].chillies.toDouble() else 0.0
        cropAllData.add(cropModelChillies)

        val cropModelColeCrops = CropModel()
        cropModelColeCrops.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelColeCrops.subcategoryData = "Cole crops"
        cropModelColeCrops.volumeData =
            if (chemicalDataCropWise[index].cole_crops != null && chemicalDataCropWise[index].cole_crops.isNotEmpty()) chemicalDataCropWise[index].cole_crops.toDouble() else 0.0
        cropAllData.add(cropModelColeCrops)

        val cropModelMelons = CropModel()
        cropModelMelons.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelMelons.subcategoryData = "Cucurbits/Melons"
        cropModelMelons.volumeData =
            if (chemicalDataCropWise[index].cucurbits_melons != null && chemicalDataCropWise[index].cucurbits_melons.isNotEmpty()) chemicalDataCropWise[index].cucurbits_melons.toDouble() else 0.0
        cropAllData.add(cropModelMelons)

        val cropModelOnion = CropModel()
        cropModelOnion.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelOnion.subcategoryData = "Onion/Garlic"
        cropModelOnion.volumeData =
            if (chemicalDataCropWise[index].onion_garlic != null && chemicalDataCropWise[index].onion_garlic.isNotEmpty()) chemicalDataCropWise[index].onion_garlic.toDouble() else 0.0
        cropAllData.add(cropModelOnion)

        val cropModelPotato = CropModel()
        cropModelPotato.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelPotato.subcategoryData = "Potato"
        cropModelPotato.volumeData =
            if (chemicalDataCropWise[index].potato != null && chemicalDataCropWise[index].potato.isNotEmpty()) chemicalDataCropWise[index].potato.toDouble() else 0.0
        cropAllData.add(cropModelPotato)

        val cropModelTomato = CropModel()
        cropModelTomato.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelTomato.subcategoryData = "Tomato"
        cropModelTomato.volumeData =
            if (chemicalDataCropWise[index].tomato != null && chemicalDataCropWise[index].tomato.isNotEmpty()) chemicalDataCropWise[index].tomato.toDouble() else 0.0
        cropAllData.add(cropModelTomato)

        val cropModelOtherVeg = CropModel()
        cropModelOtherVeg.categoryData =
            categoryList.find { it.name.equals("Vegetables", ignoreCase = true) }
        cropModelOtherVeg.subcategoryData = "Other Veg"
        cropModelOtherVeg.volumeData =
            if (chemicalDataCropWise[index].other_veg != null && chemicalDataCropWise[index].other_veg.isNotEmpty()) chemicalDataCropWise[index].other_veg.toDouble() else 0.0
        cropAllData.add(cropModelOtherVeg)

        val cropModelOther = CropModel()
        cropModelOther.categoryData =
            categoryList.find { it.name.equals("Others", ignoreCase = true) }
        cropModelOther.subcategoryData = "Others"
        cropModelOther.volumeData =
            if (chemicalDataCropWise[index].others != null && chemicalDataCropWise[index].others.isNotEmpty()) chemicalDataCropWise[index].others.toDouble() else 0.0
        cropAllData.add(cropModelOther)
        filterCropAllData.clear()
        totalPercentage = 0.0
        cropAllData.forEach {
            if (it.volumeData != 0.0) {
                filterCropAllData.add(it)
                totalPercentage += it.volumeData
            }
        }
//        txtChemicalSelectClass.text = classData.name
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
                this@ChemicalViewCropWiseActivity,
                LinearLayoutManager.VERTICAL,
                false
            )

        listAdapter = ChemicalCropWiseItemAdapter(
            filterCropAllData,
            categories,
            this@ChemicalViewCropWiseActivity
        )
        rcvCropViewItems.layoutManager = linearLayoutManager
        rcvCropViewItems.adapter = listAdapter
        hideProgress()
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
        listAdapter.setCropData(subCategories)
        listAdapter.notifyDataSetChanged()
    }

    private fun updateCropWiseData() {
        showProgress()
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
        query["state_id"] = chemicalDataCropWise[index].id
        query["id"] = stateId
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
        query["volume"] =
            if (!TextUtils.isEmpty(formulationData.volume)) formulationData.volume.toString()
                .replace(",", "").toDouble().toString() else "0.0"

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .updatedCropData(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AddCropResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    makeToast(e.localizedMessage)
                    hideProgress()
                }

                override fun onNext(listBean: AddCropResponse) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        makeToast(listBean.message)
                        finish()
                    }
                }
            })
    }

    override fun onAllDataCollect(
        category: String,
        subcategory: String,
        volume: String,
        model: CropModel,
        position: Int
    ) {
        this.categoryList.find { it.name.equals(category, ignoreCase = true) }
            ?.let {
                filterCropAllData[position].categoryData = it
            }
        this.subCategory.find { it.name.equals(subcategory, ignoreCase = true) }
            ?.let {
                filterCropAllData[position].subcategoryData = it.name
            }
        filterCropAllData[position].volumeData = if (volume.isNotBlank()) volume.toDouble() else 0.0
        totalPercentage = 0.0

        filterCropAllData.forEach {
            totalPercentage += it.volumeData
        }
        if(totalPercentage == 100.0){
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.progess_green))
        }else{
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.red))
        }
        tvTotalValuePercentage.text =
            "Total " + String.format("%.1f", totalPercentage) + "%  "
    }

    override fun onCategorySelected(category: String, position: Int) {
        categoryList.find { it.name.equals(category, ignoreCase = true) }?.fc_id?.let {
            getCropsData(it, position)
        }
    }

    override fun onDeleteClick(position: Int) {
        filterCropAllData.remove(filterCropAllData[position])
        totalPercentage = 0.0
        filterCropAllData.forEach {
            totalPercentage += it.volumeData
        }
        if(totalPercentage == 100.0){
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.progess_green))
        }else{
            tvTotalValuePercentage.setBackgroundColor(resources.getColor(R.color.red))
        }
        tvTotalValuePercentage.text =
            "Total " + String.format("%.1f", totalPercentage) + "%  "
        listAdapter.notifyDataSetChanged()
    }

    override fun onEditClick(position: Int) {
        filterCropAllData[position].isEdit = true
        listAdapter.notifyDataSetChanged()
    }

    override fun onAddClick(position: Int) {
        if (totalPercentage <= 100.0) {
            filterCropAllData.add(CropModel())
            listAdapter.notifyDataSetChanged()
        } else {
            makeToast("Total percentage value cannot exceed 100.")
        }
    }
}