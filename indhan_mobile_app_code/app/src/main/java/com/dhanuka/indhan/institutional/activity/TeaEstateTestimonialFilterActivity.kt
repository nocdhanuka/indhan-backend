package com.dhanuka.indhan.institutional.activity

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.influencers.InfluencersData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillData
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgData
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.tea_estate_testimonial_filter.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class TeaEstateTestimonialFilterActivity : BaseActivity() {
    private var teaEstateType: Int = -1
    private var teaEstateId: String = ""
    private var sugarMillId: String = ""
    private var influencerId: String = ""
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var selectTeaEstate: NumberSpinner? = null
    private var selectSugarMill: NumberSpinner? = null
    private var selectInfluencer: NumberSpinner? = null
    private var selectTypeEstate: NumberSpinner? = null
    private var teaEstateList: ArrayList<TeaEstateStgData> = ArrayList()
    private var sugarMillList: ArrayList<SugarMillData> = ArrayList()
    private var influencersList: ArrayList<InfluencersData> = ArrayList()

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.spinnerEstateType -> {
                selectTypeEstate?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinnerTeaEstate -> {
                selectTeaEstate?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinnerSugarMill -> {
                selectSugarMill?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinnerInfluencer -> {
                selectInfluencer?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.btnClear -> {
                cleanFilter()
            }
            R.id.btnSubmit -> {
                applyFilter()
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    private fun applyFilter() {
        val intent = Intent()
        intent.putExtra("tea_estate_id", teaEstateId)
        intent.putExtra("sugarmill_id", sugarMillId)
        intent.putExtra("influencer_id", influencerId)
        intent.putExtra("tea_estate_type", teaEstateType)
        setResult(RESULT_OK, intent)
        finish()
    }

    private fun cleanFilter() {
        teaEstateType = -1
        teaEstateId = ""
        sugarMillId = ""
        influencerId = ""
        val intent = Intent()
        intent.putExtra("tea_estate_id", teaEstateId)
        intent.putExtra("sugarmill_id", sugarMillId)
        intent.putExtra("influencer_id", influencerId)
        intent.putExtra("tea_estate_type", teaEstateType)
        setResult(RESULT_OK, intent)
        finish()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.tea_estate_testimonial_filter)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        setListener()
        getLocalData()
    }

    private fun setListener() {
        spinnerTeaEstate.visibility = View.GONE
        spinnerSugarMill.visibility = View.GONE
        ivBack.setOnClickListener(this)
        btnSubmit.setOnClickListener(this)
        btnClear.setOnClickListener(this)
        spinnerSugarMill.setOnClickListener(this)
        spinnerInfluencer.setOnClickListener(this)
        spinnerTeaEstate.setOnClickListener(this)
        spinnerEstateType.setOnClickListener(this)
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                teaEstateList.clear()
                (appDatabase?.teaEstateStgListDataDao()?.allTeaEstate
                    ?: ArrayList<TeaEstateStgData>()).forEach() {
                    teaEstateList.add(it!!)
                }

                sugarMillList.clear()
                (appDatabase?.sugarMillListDataDao()?.allSugarMill
                    ?: ArrayList<SugarMillData>()).forEach() {
                    sugarMillList.add(it!!)
                }

                influencersList.clear()
                (appDatabase?.influencersListDataDao()?.allInfluencers
                    ?: ArrayList<InfluencersData>()).forEach() {
                    influencersList.add(it!!)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setDropDown()
                }
            }
        }
    }

    private fun setDropDown() {

        val teaEstateStringList = ArrayList<String>()
        teaEstateList.forEach() {
            it.name?.let { it1 -> teaEstateStringList.add(it1) }
        }

        val sugarMillStringList = ArrayList<String>()
        sugarMillList.forEach() {
            it.name?.let { it1 -> sugarMillStringList.add(it1) }
        }

        val influencerStringList = ArrayList<String>()
        influencersList.forEach() {
            it.name?.let { it1 -> influencerStringList.add(it1) }
        }


        selectTeaEstate = NumberSpinner(this, teaEstateStringList, "Select Tea Estate/STG")
        selectTeaEstate?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerTeaEstate.text = item
                teaEstateId = teaEstateList[position].tea_estate_id.toString()
            }
        })

        selectSugarMill = NumberSpinner(this, sugarMillStringList, "Select Sugar Mill")
        selectSugarMill?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerSugarMill.text = item
                sugarMillId = sugarMillList[position].sugarmill_id.toString()
            }
        })


        selectInfluencer = NumberSpinner(this, influencerStringList, "Select Influencer")
        selectInfluencer?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerInfluencer.text = item
                influencerId = influencersList[position].influencer_id.toString()
            }
        })


        val arrayListType = ArrayList<String>()
        arrayListType.add("TeaEstate")
        arrayListType.add("STG")
        arrayListType.add("Sugar mill")
        selectTypeEstate = NumberSpinner(this, arrayListType, "Select Type")
        selectTypeEstate?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {

                spinnerEstateType.text = item
                teaEstateType =
                    if (item.equals("TeaEstate", ignoreCase = true)) 1 else if (item.equals(
                            "STG",
                            ignoreCase = true
                        )
                    ) 2 else 3
                if (teaEstateType == 1 || teaEstateType == 2) {
                    spinnerTeaEstate.visibility = View.VISIBLE
                    spinnerSugarMill.visibility = View.GONE
                } else if (teaEstateType == 3) {
                    spinnerTeaEstate.visibility = View.GONE
                    spinnerSugarMill.visibility = View.VISIBLE
                }
            }
        })

    }
}