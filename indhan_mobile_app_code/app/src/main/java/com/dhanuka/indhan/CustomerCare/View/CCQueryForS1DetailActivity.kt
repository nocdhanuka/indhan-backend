package com.dhanuka.indhan.PlanATP.View

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.BDOSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.BdoListSpinnerDialog
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterFactory
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterPresenterImpl
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesForS1Model
import com.dhanuka.indhan.CustomerCare.Presenter.CCQueryforS1DetailPresenterImpl
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.CCRequestListAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.activity_cc_query_detailfor_s1.*

class CCQueryForS1DetailActivity : BaseActivity(), BDOFilterFactory.BDOFilterView,
    CustomerCareFactory.CCQueryDetailHomeInterface {
    lateinit var userType: String
    lateinit var preference: Preference
    lateinit var presenter: CustomerCareFactory.CCQueryDetailforS1Presenter
    lateinit var presenter_bdo: BDOFilterFactory.BDOFilterPresenter
    lateinit var spinner: BDOSpinnerDialog
    lateinit var model: CCQueriesForS1Model.CustomerDatum
    var request_list: ArrayList<CCQueriesForS1Model.ResponseList> = ArrayList()
    lateinit var request_adapter: CCRequestListAdapter

    lateinit var bdoSpinnerDialog: BdoListSpinnerDialog
    var bdo_name = ""
    var bdo_id = 0
    var bdoList: ArrayList<BDOFilterResponse.BDOList> = ArrayList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_cc_query_detailfor_s1)
        preference = Preference(this)
        presenter_bdo = BDOFilterPresenterImpl(this)
        userType = preference.getString("user_type")
        iCross.setOnClickListener(this)
        spinner_bdo.setOnClickListener(this)
        call_img.setOnClickListener(this)
        submit_tv.setOnClickListener(this)
        spinner_product.setOnClickListener(this)
        presenter = CCQueryforS1DetailPresenterImpl(this)
        bdoSpinnerDialog = BdoListSpinnerDialog(this, bdoList, "Select or Search BDO")
        bdoSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_bdo.text = item
                bdo_name = item
                bdo_id = bdoList.get(position).bdoID
            }
        })
        val bundle = intent.extras
        if (bundle != null) {
            model = bundle.getSerializable("cc_model") as CCQueriesForS1Model.CustomerDatum
            request_list =
                bundle.getSerializable("request_model") as ArrayList<CCQueriesForS1Model.ResponseList>


        }
        if (model != null) {
            ref_no.text = "Reference No. #" + model.tokenNumber
            query_type.text = model.typeOfCall
            name.text = model.farmername
            address_tv.text =
                model.village + ", " + model.tehsil + ", " + model.district + ", " + model.state
            contact_tv.text = model.mobile
            description_tv.text = model.remark
            date_tv.text = model.datetimeFormat
            spinner_bdo.text = model.name

            if (!model.product.trim().isNullOrEmpty()) {
                prod.visibility = View.VISIBLE
                spinner_product.text = model.product
                quantity_et.visibility = View.GONE
            } else {
                prod.visibility = View.GONE

            }

            if (model.status.equals("Closed")) {
                bdoLayout.visibility = View.GONE
                prod.visibility = View.GONE
                submit_tv.setOnClickListener(null)
                submit_tv.setBackgroundColor(ContextCompat.getColor(this, R.color.darkgray))
            } else {
                submit_tv.setBackgroundColor(ContextCompat.getColor(this, R.color.colorPrimary))
                submit_tv.setOnClickListener(this)
                bdoLayout.visibility = View.VISIBLE
                prod.visibility = View.VISIBLE

            }
        }
        request_adapter = CCRequestListAdapter(request_list)
        //   request_adapter.setClickListener(this)
        history_View.adapter = request_adapter
        history_View.layoutManager =
            LinearLayoutManager(this)
        history_View.scrollToPosition(0)

        call_img.visibility = View.GONE
    }

    override fun OnSuccess(response: String) {
        makeToast(response)
        if (response.equals("Response Successfully Updated!"))
            finish()
    }

    override fun onFailure(response: String) {
        makeToast(response)
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.spinner_bdo -> {
                if (!bdoSpinnerDialog.isEnable)
                    bdoSpinnerDialog.showSpinerDialog()
            }
            R.id.submit_tv -> {
                requestdata()
            }
            R.id.call_img -> {
                var intent = Intent(Intent.ACTION_DIAL)
                var str = "tel:" + model.mobile
                intent.setData(Uri.parse(str))
                startActivity(intent)
            }

        }
    }

    private fun requestdata() {

        if (checkValues().equals("perfect")) {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                var map = HashMap<String, Any>()
                map.put("apiToken", preference.getString("token"))
                map.put("query_id", model.queryId.toString())
                map.put("bdo_id", bdo_id.toString())
                presenter.OnUpdateBDO(map)
            } else
                makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun checkValues(): String {
        if (bdo_id == 0) {
            return "Please select BDO"
        }
        return "perfect"
    }

    override fun onSuccess(model: BDOFilterResponse) {
        if (userType.contains("AREA")) {
            if (model.data.bdoList != null) {
                bdoList.clear()
                if (model.data.bdoList.size > 0) {
                    bdoList.addAll(model.data.bdoList)
                }
                bdoSpinnerDialog.updatelist(bdoList)
            }
        }
    }

    override fun onResume() {
        super.onResume()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenter_bdo.onRequest(preference.getString("token"))
        } else
            makeToast(resources.getString(R.string.no_internet_connection))
    }

}
