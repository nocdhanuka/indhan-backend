package com.dhanuka.indhan.PlanATP.View

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.view.Window
import android.widget.EditText
import android.widget.TextView
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.BDOSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesModel
import com.dhanuka.indhan.CustomerCare.Presenter.CCQueryDetailPresenterImpl
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.SelfParticipationAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Reports.Model.ProductListResponse
import com.dhanuka.indhan.Reports.Reports_MVP.ProductListPresenterImpl
import com.dhanuka.indhan.Reports.Reports_MVP.ReportsFactory
import com.dhanuka.indhan.SelfParticipationActivity.Model.TicketList
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.activity_cc_query_detail.*

class CCQueryDetailActivity : BaseActivity(), CustomerCareFactory.CCQueryDetailHomeInterface,
    ReportsFactory.ProductHomeListener {

    lateinit var preference: Preference
    lateinit var adapter: SelfParticipationAdapter
    var atpList: ArrayList<TicketList> = ArrayList()
    lateinit var presenter: CustomerCareFactory.CCQueryDetailPresenter
    lateinit var spinner: BDOSpinnerDialog
    lateinit var model: CCQueriesModel.CustomerDatum
    lateinit var productSpinnerDialog: ProductSpinnerDialog
    private val product1RecordResponseModel = ArrayList<ProductBean>()
    lateinit var presenter_productlist: ReportsFactory.ProductListPresenter

    var status = ""
    var remark = ""
    var product = ""
    var quantity = ""
    var amount = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_cc_query_detail)
        preference = Preference(this)
        iCross.setOnClickListener(this)
        remark_tv.setOnClickListener(this)
        spinner_status.setOnClickListener(this)
        call_img.setOnClickListener(this)
        submit_tv.setOnClickListener(this)
        spinner_product.setOnClickListener(this)
        presenter = CCQueryDetailPresenterImpl(this)
        presenter_productlist = ProductListPresenterImpl(this)

        val bundle = intent.extras
        if (bundle != null) {
            model = bundle.getSerializable("cc_model") as CCQueriesModel.CustomerDatum
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
            if (!model.crop.isNullOrEmpty())
                spinner_crop.text = model.crop
            else
                spinner_crop.text = "No Crop Available"
            if (!model.product.isNullOrEmpty()) {
                prod.visibility = View.VISIBLE
                spinner_product.text = model.product
            } else prod.visibility = View.GONE
            spinner_status.text = model.status
        }
        if (model.mobile.isNullOrEmpty()) {
            call_img.visibility = View.GONE
        }
        productSpinnerDialog =
            ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
        productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_product.text = item
                product = item
            }
        })
        if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
            presenter_productlist.onRequestProductList(preference.getString("token"))
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
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
            R.id.spinner_status -> {
                changeStatusDialog()
            }
            R.id.remark_tv -> {
                writeRemark()
            }
            R.id.submit_tv -> {
                requestdata()
            }
            R.id.spinner_product -> {
                if (!productSpinnerDialog.isEnable)
                    productSpinnerDialog.showSpinerDialog()
            }
            R.id.call_img -> {
                var intent = Intent(Intent.ACTION_DIAL)
                var str = "tel:" + model.mobile
                intent.setData(Uri.parse(str))
                startActivity(intent)
            }

        }
    }

    private fun changeStatusDialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(android.graphics.Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_status)
        dialog.setCanceledOnTouchOutside(true)
        dialog.setCancelable(false)
        val follow = dialog.findViewById<View>(R.id.follow) as Medium
        val closed = dialog.findViewById<View>(R.id.closed) as Medium
        follow.setOnClickListener {
            dialog.dismiss()
            spinner_status.text = "Follow up"
            status = "2"
            writeRemark()
        }
        closed.setOnClickListener {
            dialog.dismiss()
            spinner_status.text = "Closed"
            status = "3"

            writeRemark()
        }
        dialog.show()
    }

    override fun onSuccess(model: ProductListResponse) {
        if (model.data.productlist != null) {
            if (model.data.productlist.size > 0) {
                product1RecordResponseModel.clear()
                model.data.productlist.forEach {
                    var id = it.productId
                    var name = it.product
                    var bean = ProductBean()
                    bean.setpID(id)
                    bean.item = name
                    product1RecordResponseModel.add(bean)
                }
                productSpinnerDialog.updatelist(product1RecordResponseModel)
            }
        }
    }

    private fun writeRemark() {
        if (model.typeOfCall.equals("Sales", false) && status.equals("3", false)) {
            values_layout.visibility = View.VISIBLE
        } else {
            values_layout.visibility = View.GONE
        }
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_reason)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.reason_tv) as EditText
        val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as TextView
        tic_banner.text = "Remark"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            var textd = ticketNumber.text.toString()
            remark = textd
            remark_tv.visibility = View.VISIBLE
            remark_tv.text = remark
            //requestdata()
        }
        dialog.show()
    }

    private fun requestdata() {
        quantity = quantity_et.text.toString()
        amount = amount_et.text.toString()
        if (checkValues().equals("perfect")) {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                var map = HashMap<String, Any>()
                map.put("apiToken", preference.getString("token"))
                map.put("status", status)
                map.put("remark", remark)
                map.put("query_id", model.queryId.toString())
                map.put("product", product)
                map.put("quantity", quantity)
                map.put("amount", amount)
                presenter.OnUpdateRemark(map)
            } else
                makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    private fun checkValues(): String {
        if (remark.isNullOrEmpty())
            return "Please Enter Some Remark"
        if (status.isNullOrEmpty())
            return "Please change the status"
        if (model.typeOfCall.equals("sales")) {
            if (product.isNullOrEmpty())
                return "Please Select some product"
            if (quantity.isNullOrEmpty())
                return "Please enter quantity"
            if (amount.isNullOrEmpty())
                return "Please enter Amount per acre"

        }
        return "perfect"
    }

}
