package com.dhanuka.indhan.Activity.DataCollection.chemical

import android.content.Intent
import android.graphics.Color
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.Constants
import com.dhanuka.indhan.Model.DistrictResponse
import com.dhanuka.indhan.Model.PieChartData
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.activity_chemical_data_collection.*
import org.eazegraph.lib.models.PieModel
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers


class ChemicalDataCollection : BaseActivity(), View.OnClickListener {
    private var districtName: String = ""
    private var stateId: String = ""
    private var districtId: String = ""
    private lateinit var preference: Preference

    private var districtStr: String = ""
    private lateinit var selectDistrict: NumberSpinner
    private var districtList = ArrayList<DistrictResponse.Data>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_chemical_data_collection)
        preference = Preference(this)
        val name = preference.getString("Username")
        txtChemicalName.text = "FL - $name"
        setListener()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getDistricts()
        } else {
            makeToast("No Internet Connection.Please check your connection.")
        }
    }

    private fun setListener() {
        txtChemicalSelectDistrict.setOnClickListener(this)

        txtAddDistrictWise.setOnClickListener(this)
        txtAddCropWise.setOnClickListener(this)
        txtAddCompanyWise.setOnClickListener(this)

        txtViewDistrictWise.setOnClickListener(this)
        txtViewCropWise.setOnClickListener(this)
        txtViewCompanyWise.setOnClickListener(this)
    }

    private fun getDistricts() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getDistricts(query)
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
        val districts = ArrayList<String>()
        districtList.forEach {
            districts.add(it.district)
        }
        selectDistrict =
            NumberSpinner(this, districts, "Search or Select District")
        if (districtStr.isNotEmpty()) {
            val districtObject: DistrictResponse.Data =
                districtList.find { it.district == districtStr }!!

            districtName = districtObject.district
            districtId = districtObject.district_id.toString()
            stateId = districtObject.state_id.toString()
            getChemicalCompanyWise(districtId)
        }
        selectDistrict.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                districtStr = item
                txtChemicalSelectDistrict.text = item

                val districtObject: DistrictResponse.Data =
                    districtList.find { it.district == districtStr }!!

                districtName = districtObject.district
                districtId = districtObject.district_id.toString()
                stateId = districtObject.state_id.toString()
                getChemicalCompanyWise(districtId)
            }
        })
    }


    private fun setPieChartValue(
        companyDataTypeOne: PieChartData
    ) {
        piechart.clearChart()

        piechart.clearAnimation()

        llDistrictWise.visibility = View.VISIBLE
        llCropWise.visibility = View.GONE
        llCompanyWise.visibility = View.GONE
        tvFormulationTotalValue.text = "TOTAL - " + companyDataTypeOne.total_formulation.toString()
        tvCompletedValue.text = companyDataTypeOne.total_companyData.toString()
        piechart.addPieSlice(
            PieModel(
                "Completed",
                Integer.parseInt(companyDataTypeOne.total_companyData.toString()).toFloat(),
                Color.parseColor("#0CA160")
            )
        )

        piechart.addPieSlice(
            PieModel(
                "Not Completed",
                Integer.parseInt((companyDataTypeOne.total_formulation - companyDataTypeOne.total_companyData).toString())
                    .toFloat(),
                Color.parseColor("#EF5350")
            )
        )
        tvInCompletedValue.text =
            (companyDataTypeOne.total_formulation - companyDataTypeOne.total_companyData).toString()

        piechart.startAnimation()

    }

    private fun getChemicalCompanyWise(districtId: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["ecnnumber"] = preference.getString("ecn")
        query["district_id"] = districtId
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getChemicalChartData(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PieChartData>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: PieChartData) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        setPieChartValue(listBean)
                    }

                }
            })
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtChemicalSelectDistrict -> {
                if (districtList.size > 0) {
                    if (!selectDistrict.isEnable) {
                        selectDistrict.showSpinerDialog()
                    }
                } else {
                    makeToast("No district found.")
                }
            }
            R.id.txtAddDistrictWise -> {
                val bundle = Bundle();
                bundle.putString("district_id", districtId)
                bundle.putString("state_id", stateId)
                bundle.putString("district_name", txtChemicalSelectDistrict.text.toString())
                val intent = Intent(activity, ChemicalAddDistrictWiseActivity::class.java)
                intent.putExtras(bundle)
                startActivityForResult(intent, Constants.ACTIVITY_RESULT_DISTRICT)
            }
            R.id.txtViewDistrictWise -> {
                val bundle = Bundle();
                bundle.putString("district_id", districtId)
                bundle.putString("state_id", stateId)
                bundle.putString("district_name", txtChemicalSelectDistrict.text.toString())
                launchIntent(ChemicalViewDistrictWiseActivity::class.java, bundle, false)
            }
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == Constants.ACTIVITY_RESULT_DISTRICT) {
                getDistricts()
            }
        }
    }

}