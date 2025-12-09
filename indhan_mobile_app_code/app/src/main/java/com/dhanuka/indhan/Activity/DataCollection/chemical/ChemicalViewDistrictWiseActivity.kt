package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.os.Bundle
import android.view.MenuInflater
import android.view.MenuItem
import android.view.View
import android.widget.PopupMenu
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.DataCollection.chemical.adapter.ChemicalDistrictWiseItemAdapter
import com.dhanuka.indhan.Model.ChemicalDistrictWiseDataList
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_chemical_view_crop_wise.*
import kotlinx.android.synthetic.main.activity_chemical_view_district_wise.*
import kotlinx.android.synthetic.main.activity_chemical_view_district_wise.imgFilter
import kotlinx.android.synthetic.main.activity_chemical_view_district_wise.tvDistrictName
import kotlinx.android.synthetic.main.activity_chemical_view_district_wise.tvFyUser
import kotlinx.android.synthetic.main.activity_chemical_view_district_wise.txtFilterChemical
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ChemicalViewDistrictWiseActivity : BaseActivity(),
    ChemicalDistrictWiseItemAdapter.ChemicalDistrictWiseAdapterListener,
    PopupMenu.OnMenuItemClickListener {

    private var formulationStr: String = ""
    private var selectFormulation: NumberSpinner? = null
    private lateinit var preference: Preference
    private var chemicalDataDistrictWise = ArrayList<ChemicalDistrictWiseDataList.Data>()
    private var chemicalFilterDataDistrictWise = ArrayList<ChemicalDistrictWiseDataList.Data>()
    private lateinit var listAdapter: ChemicalDistrictWiseItemAdapter
    private var districtName = ""
    private var listFormulation = ArrayList<String>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_view_district_wise)
        intent?.getStringExtra("district_name")?.let {
            districtName = it
            tvDistrictName.text = "For District - $districtName"
        }
        imgFilter.setOnClickListener(this)
        txtFilterChemical.setOnClickListener(this)
        preference = Preference(this)
        tvFyUser.text = Utility.financialYear
        getChemicalDistrictWise()
    }

    private fun getChemicalDistrictWise() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .chemicalDistrictWiseRecord(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ChemicalDistrictWiseDataList>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ChemicalDistrictWiseDataList) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        chemicalDataDistrictWise.clear()
                        if (listBean.data.size > 0) {
                            listBean.data.forEach {
                                if (it.district.equals(districtName, ignoreCase = true)) {
                                    chemicalDataDistrictWise.add(it)
                                    chemicalFilterDataDistrictWise.add(it)
                                    if (!listFormulation.contains("All")) {
                                        listFormulation.add("All")
                                    }
                                    listFormulation.add(it.formulationname)
                                }
                            }
                        }
                        setAdapter()
                    }
                }
            })
    }

    private fun setAdapter() {
        selectFormulation =
            NumberSpinner(this, listFormulation, "Search or select formulation")
        selectFormulation?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                chemicalFilterDataDistrictWise.clear()
                if (item.equals("All", ignoreCase = true)) {
                    formulationStr = ""
                    txtFilterChemical.text = ""
                    chemicalFilterDataDistrictWise.addAll(chemicalDataDistrictWise)
                    listAdapter.notifyDataSetChanged()
                } else {
                    formulationStr = item
                    txtFilterChemical.text = item
                    chemicalDataDistrictWise.forEach {
                        if (it.formulationname.equals(item, ignoreCase = true)) {
                            chemicalFilterDataDistrictWise.add(it)
                        }
                        listAdapter.notifyDataSetChanged()
                    }
                }
            }
        })


        listAdapter = ChemicalDistrictWiseItemAdapter(
            chemicalFilterDataDistrictWise,
            this@ChemicalViewDistrictWiseActivity
        )
        rvChemicalDistrictWise.adapter = listAdapter
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.imgFilter -> {
                showPopup(v)
            }

            R.id.txtFilterChemical -> {
                if (selectFormulation != null)
                    if (!(selectFormulation!!.isEnable)) {
                        selectFormulation?.showSpinerDialog()
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
                val districtId = intent?.getStringExtra("district_id")
                val stateId = intent?.getStringExtra("state_id")
                bundle.putString("district_id", districtId)
                bundle.putString("state_id", stateId)
                bundle.putString("district_name", districtName)
                launchIntent(ChemicalViewCompanyWiseActivity::class.java, bundle, true)
                true
            }
            R.id.crop -> {
                val bundle = Bundle();
                val districtId = intent?.getStringExtra("district_id")
                val stateId = intent?.getStringExtra("state_id")
                bundle.putString("district_id", districtId)
                bundle.putString("state_id", stateId)
                bundle.putString("district_name", districtName)
                launchIntent(ChemicalViewCropWiseActivity::class.java, bundle, true)
                true
            }
            R.id.district -> {
                false
            }
            else -> false
        }
    }

    override fun onItemSelected(model: ChemicalDistrictWiseDataList.Data) {
        val districtId = intent?.getStringExtra("district_id")
        val stateId = intent?.getStringExtra("state_id")
        val bundle = Bundle();
        bundle.putString("district_id", districtId)
        bundle.putString("state_id", stateId)
        bundle.putString("from", "edit")
        bundle.putString("edit_data", Gson().toJson(model))
        launchIntent(ChemicalAddDistrictWiseActivity::class.java, bundle, false)
    }
}