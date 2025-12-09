package com.dhanuka.indhan.PlanATP.View

import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesForS1Model
import com.dhanuka.indhan.CustomerCare.Presenter.CCSPresenterImpl
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.CCListAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_customer_care_normal.*
import java.util.*
import kotlin.collections.ArrayList

/**
 * Created by John on 7/23/2018.
 */
class CustomerCareS1Activity : BaseActivity(), CustomerCareFactory.CCSHomeInterface,
    CCListAdapter.CCListClickListener {

    lateinit var preference: Preference
    lateinit var presenter: CustomerCareFactory.CCSPresenter
    lateinit var adapter: CCListAdapter
    var userType = ""
    var adapterList: ArrayList<CCQueriesForS1Model.CustomerDatum> = ArrayList()
    var record: ArrayList<CCQueriesForS1Model.ResponseList> = ArrayList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_customer_care_normal)
        preference = Preference(this)
        iCross.setOnClickListener(this)
        userType = preference.getString("user_type")
        presenter = CCSPresenterImpl(this)
        adapter = CCListAdapter(adapterList)
        adapter.setClickListener(this)
        rv_cc.layoutManager =
            LinearLayoutManager(this)
        rv_cc.adapter = adapter
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }

    override fun onResume() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenter.onRequestQueries(preference.getString("token"))
        } else
            makeToast(resources.getString(R.string.no_internet_connection))
        super.onResume()
    }

    override fun OnSuccess(response: CCQueriesForS1Model) {
        val list = response.data.customerData
        if (list.size > 0) {
            list.forEach {
                it.date = Utility.getDateFromCCDateTimeFormat(it.datetimeFormat) as Date?
                it.date_str = Utility.getDateStringforCC(it.date)
                it.time = Utility.getTimeStringforCC(it.date)
            }
            adapterList.clear()
            adapterList.addAll(list)

        }
        if (response.data.responseList != null) {
            record.clear()
            record.addAll(response.data.responseList)
        }
        adapter.notifyDataSetChanged()
    }

    override fun onFailure(response: String) {
        makeToast(response)
    }

    override fun onSelected(model: CCQueriesForS1Model.CustomerDatum) {
        var record_send: ArrayList<CCQueriesForS1Model.ResponseList> = ArrayList()
        record_send.clear()
        if (record.size > 0) {
            record.forEach {
                if (it.tokenNumber.equals(model.tokenNumber)) {
                    record_send.add(it)
                }
            }
        }
        // if (!model.status.equals("closed", true)) {
        val b = Bundle()
        b.putSerializable("cc_model", model)
        b.putSerializable("request_model", record_send)
        launchIntent(CCQueryForS1DetailActivity::class.java, b, false)
        //}
    }
}

