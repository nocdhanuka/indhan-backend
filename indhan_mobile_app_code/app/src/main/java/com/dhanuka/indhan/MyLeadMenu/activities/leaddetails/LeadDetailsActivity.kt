package com.dhanuka.indhan.MyLeadMenu.activities.leaddetails

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.adapters.RelatedLeadAdapter
import com.dhanuka.indhan.MyLeadMenu.dialog.ChangeStatusDialog
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadData
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.TimeStamp
import com.dhanuka.indhan.validatefarmers.SuccessBean
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_lead_details.*
import kotlinx.android.synthetic.main.item_open_leads.view.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class LeadDetailsActivity : BaseActivity() {
    override fun onClick(v: View?) {
    }

    var leadId: String = ""
    var fromOpenOrClose: String = ""

    private lateinit var preference: Preference
    private lateinit var leadData: OpenLeadData
    private lateinit var adapter: RelatedLeadAdapter
    private lateinit var leadDetailsBeanTemp: LeadDetailsBean

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_lead_details)
        preference = Preference(this)

        //apply click on view
        applyClickOnView()
        //get data for showing lead detail
        getDataForLeadDetail()
    }

    //get data for showing lead detail
    private fun getDataForLeadDetail() {
        if (intent.hasExtra(Constants.LESD_DETAIL_DATA)) {
            leadData = intent.extras?.get(Constants.LESD_DETAIL_DATA) as OpenLeadData
            leadId = leadData.lead_id
        }
        if (intent.hasExtra(Constants.FROM)) {
            fromOpenOrClose = intent.extras?.get(Constants.FROM) as String
            if (fromOpenOrClose.equals("close")) {
                txtCurrentStatus.visibility = View.VISIBLE
                txtChangeStatus.visibility = View.GONE
            } else {
                txtCurrentStatus.visibility = View.GONE
                txtChangeStatus.visibility = View.VISIBLE

            }
        }
        if (intent.hasExtra("leadId")) {
            leadId = intent?.getStringExtra("leadId") ?: ""
        }

        getLeadDetails()

    }

    //show lead detail

    private fun showLeadDetail(leadData: LeadDetailsBean.Data) {


        if (leadData.leadStatus.equals("2")) {
            txtCurrentStatus.text = "Sold"

        } else if (leadData.leadStatus.equals("3")) {
            txtCurrentStatus.text = "Not interested"

        }
        leadId = leadData.leadId
        txtLeadName.text = leadData.leadName
        txtVillageName.text = leadData.villageName

        txtLeadId.text = resources.getString(R.string.lead_id) + " " + leadData.leadCode

        if (leadData.distributorName != null) {
            lblDistributor.visibility = View.VISIBLE
            txtDistributorName.visibility = View.VISIBLE
            txtDistributorName.text = leadData.distributorName.toString()

        } else {
            lblDistributor.visibility = View.GONE
            txtDistributorName.visibility = View.GONE

        }
        if (leadData.purchasedFrom != null) {
            lbPurchasedFrom.visibility = View.VISIBLE
            txtPurchasedFrom.visibility = View.VISIBLE
            txtPurchasedFrom.text = leadData.purchasedFrom.toString()
        } else {
            lbPurchasedFrom.visibility = View.GONE
            txtPurchasedFrom.visibility = View.GONE

        }
        if (leadData.cropdata.size > 0) {
            if (leadData.cropdata.get(0) != null) {
                txtCropName.text = leadData.cropdata.get(0).crop
            }
        }
        txtProductName.text = leadData.product
        txtQuantityToBePurchased.text = leadData.purchasedArea
        txtExpectedDateToPurchase.text = getTime(leadData.expectedPurchaseDate)
        if (leadData.contactNumber != null && !leadData.contactNumber.equals("")) {
            txtMobileNo.text = "+91 " + hideSomeDigit(leadData.contactNumber)
            txtCallNow.visibility = View.VISIBLE
        } else {
            txtMobileNo.text = "XX XXXXXXXXXX"
            txtCallNow.visibility = View.GONE
        }
        txtCallNow.setOnClickListener {
            if (leadData.callStatus.equals("0")) {
                if (leadData.contactNumber != null && !leadData.contactNumber.equals("")) {
                    val intent = Intent(Intent.ACTION_DIAL)
                    intent.data = Uri.parse("tel:" + leadData.contactNumber)
                    startActivity(intent)
                }
            } else {
                if (leadData.contactNumber != null && !leadData.contactNumber.equals("")) {
                    callSatatus(leadData.farmerId, leadData.farmerType, leadData.contactNumber)
                }
                // Toast.makeText(this, "You have already called!", Toast.LENGTH_LONG).show()
            }
        }

        //get difference between two date
        var currentDate = getCurrentDate()
        if (!leadData.expectedPurchaseDate.equals("0000-00-00")) {
            val simpleDateFormat = SimpleDateFormat("yyyy-MM-dd")
            val date1 = simpleDateFormat.parse(currentDate)
            val date2 = simpleDateFormat.parse(leadData.expectedPurchaseDate)
            var diff: Long = TimeStamp.printDifference(date1, date2)

            if (diff >= 0 && diff <= 2) {
                imgHot.visibility = View.VISIBLE
                txtHot.visibility = View.VISIBLE
            } else {
                imgHot.visibility = View.GONE
                txtHot.visibility = View.GONE
            }

        } else {
            imgHot.visibility = View.GONE
            txtHot.visibility = View.GONE

        }
        if (leadData.callStatus.equals("0")) {
            imgCall.setImageResource(R.drawable.call_ylw)
        } else {
            imgCall.setImageResource(R.drawable.call_green)
        }

        adapter = RelatedLeadAdapter(this, leadData.farmerleadhistory, object : OnClickListener {
            override fun onClick(position: Int) {
                var i = Intent(this@LeadDetailsActivity, LeadDetailsActivity::class.java)
                i.putExtra("leadId", leadDetailsBeanTemp.data.farmerleadhistory[position].leadId)
                startActivity(i)
                finish()
            }
        })
        recyclerViewLead.layoutManager =
            LinearLayoutManager(this)
        recyclerViewLead.adapter = adapter
    }

    fun hideSomeDigit(input: String): String {
        val mChars = input.toCharArray()
        val count = mChars.size
        val builder = StringBuilder(count)
        for (i in 0 until count) {
            val current = mChars[i].toString()
            if (i < 7) {
                builder.append(current)

            } else {

                builder.append("X")
            }
        }

        return builder.toString()
    }

    internal fun getTime(date: String): String {
        val finalTime = date
        var date1: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd")
        try {
            date1 = sdf.parse(date)
        } catch (e: ParseException) {
            e.printStackTrace()
        }

        val timeStr: String
        val inputFormat = SimpleDateFormat("dd-MMM-yyyy")
        timeStr = inputFormat.format(date1)
        return timeStr
    }

    //apply click on view
    private fun applyClickOnView() {
        txtChangeStatus.setOnClickListener {
            ChangeStatusDialog(this@LeadDetailsActivity, leadId).show()
        }
    }

    internal fun getCurrentDate(): String {
        val c = Calendar.getInstance( Locale.ENGLISH).time
        println("Current time => $c")

        val df = SimpleDateFormat("yyyy-MM-dd")
        val formattedDate = df.format(c)
        return formattedDate
    }

    /*
    * get liead details
    * */

    private fun getLeadDetails() {
        showProgress()
        val query = HashMap<String, Any>()
        query.put("apiToken", preference.getString("token"))
        query.put("lead_id", leadId)//1 for open lead

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).BdoLeadDetail(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<LeadDetailsBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(leadDetailsBean: LeadDetailsBean) {
                    hideProgress()

                    if (leadDetailsBean.statusCode == 200) {
                        showLeadDetail(leadDetailsBean.data)
                        leadDetailsBeanTemp = leadDetailsBean
                    }
                }
            })
    }

    //call status api
    private fun callSatatus(farmerId: String, farmerType: String, contactNo: String) {
        showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("farmerId", farmerId)
        query.put("farmer_type", farmerType)
        query.put("status", "1")


        val gson = Gson()
        val json = gson.toJson(query)

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).updateCallStatus(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")

                }

                override fun onNext(openLead: SuccessBean) {
                    hideProgress()
                    if (openLead.statusCode == 200) {
                        var intent = Intent(Intent.ACTION_DIAL)
                        intent.data = Uri.parse("tel:" + contactNo)
                        startActivity(intent)
                    }
                }
            })
    }
}
