package com.dhanuka.indhan.institutional.activity.teaEstate

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllDistrictBean
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllStateBean
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.activity_filter_tea_estate.*
import kotlinx.coroutines.*

class TeaEstateFilterActivity : BaseActivity() {

    private var stateModelLis: ArrayList<AllStateBean.StateData> = ArrayList()
    private var districtList: ArrayList<AllDistrictBean.DistrictData> = ArrayList()
    private var typeList: ArrayList<String> = ArrayList()
    private var districtSpinner: NumberSpinner? = null
    private var selectType: NumberSpinner? = null
    private var stateSpinner: NumberSpinner? = null
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var estate_type = -1
    private var state_id = ""
    private var state = ""
    private var district_id = ""
    private var district = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnSubmit -> {
                applyFilter()
            }
            R.id.btnClear -> {
                clearData()
            }
            R.id.spinnerDistrict -> {
                districtSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinnerState -> {
                stateSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinnerEstateType -> {
                selectType?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    private fun applyFilter() {
        val intent = Intent()
        intent.putExtra("estate_type", estate_type)
        intent.putExtra("state", state)
        intent.putExtra("state_id", state_id)
        intent.putExtra("district", district)
        intent.putExtra("district_id", district_id)
        setResult(RESULT_OK, intent)
        finish()
    }

    private fun clearData() {
        estate_type = -1
        state = ""
        district = ""
        state_id = ""
        district_id = ""
        spinnerState.text = ""
        spinnerDistrict.text = ""
        spinnerEstateType.text = ""
        val intent = Intent()
        intent.putExtra("estate_type", estate_type)
        intent.putExtra("state", state)
        intent.putExtra("state_id", state_id)
        intent.putExtra("district", district)
        intent.putExtra("district_id", district_id)
        setResult(RESULT_OK, intent)
        finish()

    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_filter_tea_estate)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        intent?.getStringExtra("from")?.let {
            bdo.text  = "Filter ".plus(it)

            if(it.equals("Sugar Mill",ignoreCase = true)){
                spinnerEstateType.visibility = View.GONE
                estate_type = 1
            }else if (it.equals("Influencers",ignoreCase = true)){
                spinnerEstateType.visibility = View.GONE
                estate_type = 1
            }
        }
        setListeners()
        getLocalData()
    }

    private fun getLocalData() {
        intent?.getStringExtra("state")?.let {
            state = it
            spinnerState.text = it
        }

        intent?.getStringExtra("state_id")?.let {
            state_id = it
        }

        intent?.getStringExtra("district_id")?.let {
            district_id = it
        }

        intent?.getStringExtra("district")?.let {
            district = it
            spinnerDistrict.text = it
        }

        intent?.getIntExtra("estate_type", -1)?.let {
            estate_type = it
            spinnerEstateType.text = if (it == 1) "TeaEstate" else if (it == 2) "STG" else ""
        }

        stateModelLis.clear()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val state = appDatabase?.stateDao()?.allStateList
                state?.let { stateModelLis.addAll(it) }
            }.run {
                delay(300)
                withContext(Dispatchers.Main) {
                    typeList.clear()
                    typeList.add("TeaEstate")
                    typeList.add("STG")
                    setSpinnerData()
                }
            }

        }
    }

    private fun setSpinnerData() {
        selectType = NumberSpinner(this, typeList, "Select Level")
        selectType?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerEstateType.text = item
                estate_type = if (item == "TeaEstate") 1 else 2
            }
        })

        val stateListString = java.util.ArrayList<String>()
        stateModelLis.forEach() {
            it.state?.let { it1 -> stateListString.add(it1) }
        }
        stateSpinner = NumberSpinner(this, stateListString, "Select State")
        stateSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (stateModelLis.size > 0) {
                    state = item
                    state_id = stateModelLis[position].state_id ?: "0"
                    spinnerState.text = item
                    GlobalScope.launch {
                        val districtListString = java.util.ArrayList<String>()
                        withContext(Dispatchers.IO) {
                            val updatedVal =
                                appDatabase?.districtDao()?.getAllDistrictList(state_id.toInt())
                            districtList.clear()
                            updatedVal?.let { districtList.addAll(it) }
                            updatedVal?.forEach() {
                                it.district?.let { it1 -> districtListString.add(it1) }
                            }
                        }.run {
                            withContext(Dispatchers.Main) {
                                setSpinnerDistrict(districtListString)
                            }
                        }
                    }
                }
            }
        })
    }

    private fun setSpinnerDistrict(districtListString: java.util.ArrayList<String>) {
        districtSpinner = NumberSpinner(this, districtListString, "Select District")
        districtSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (districtList.size > 0) {
                    district = item
                    district_id = (districtList[position].district_id ?: "").toString()
                    spinnerDistrict.text = item
                }
            }
        })
    }

    private fun setListeners() {
        btnSubmit.setOnClickListener(this)
        spinnerDistrict.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        spinnerState.setOnClickListener(this)
        spinnerEstateType.setOnClickListener(this)
        btnClear.setOnClickListener(this)
    }
}