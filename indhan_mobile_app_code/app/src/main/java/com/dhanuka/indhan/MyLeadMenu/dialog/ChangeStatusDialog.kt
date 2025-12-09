package com.dhanuka.indhan.MyLeadMenu.dialog

import android.app.Dialog
import android.content.Context
import android.content.Intent
import android.os.Bundle
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import android.view.*
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.ListPopupWindow
import android.widget.Toast
import com.dhanuka.indhan.Activity.NewTicketMVP.DistributorSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.RetailerSpinnerDialog
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.MyLeadMenu.models.CloseLeadResponse
import com.dhanuka.indhan.MyLeadMenu.models.DistributorResponse
import com.dhanuka.indhan.MyLeadMenu.models.RetailerResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnGetId
import com.google.gson.Gson
import kotlinx.android.synthetic.main.dialog_current_status.*
import kotlinx.android.synthetic.main.dialog_for_ask_surity.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class ChangeStatusDialog(context: Context, leadId: String) : Dialog(context) {
    var arrayList: ArrayList<String> = ArrayList()
    var listPurchasedFrom: ArrayList<String> = ArrayList()
    var sums: Double = 0.0
    var leadId: String = ""
    var distributor: String = ""
    var purchaseFrom: String = ""
    var purchaseArea: String = ""
    var distributorList: ArrayList<DistributorResponse.Data> = ArrayList()
    var retailerList: ArrayList<RetailerResponse.Data> = ArrayList()
    var apiToken = ""
    var distributorId = ""
    var retailerId = ""

    private lateinit var preference: Preference
    lateinit var distributorSpinnerDialog: DistributorSpinnerDialog
    lateinit var retailerSpinnerDialog: RetailerSpinnerDialog

    init {
        this.leadId = leadId
    }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        this.requestWindowFeature(Window.FEATURE_NO_TITLE)
        this.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        this.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        setContentView(LayoutInflater.from(context).inflate(R.layout.dialog_current_status, null))
        this.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.MATCH_PARENT
        )
        // this.window!!.setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN)
        this.window!!.setBackgroundDrawableResource(android.R.color.transparent)

        preference = Preference(context)
        apiToken = preference.getString("token")

        getDistributorApi(apiToken)
        //pupulate data for purchased acres
        showPurchasedAcreArea()
        //apply click on view
        applyCLickOnView()


    }


    private fun showPurchasedAcreArea() {
        for (items in 0..99) {
            sums = sums + 0.5
            arrayList.add("" + sums + " Acre")
        }
    }

    private fun applyCLickOnView() {
        txtLeadSold.setOnClickListener {

            if (validateForm()) {
                soldLead(purchaseFrom, "" + purchaseArea, "2")
            }
        }
        txtNotIntrested.setOnClickListener {
            surityDialog()
            dismiss()
/*
            if (validateForm()) {
                soldLead(purchaseFrom, "" + purchaseArea, "3")
            }*/
        }
        llPurchasedArea.setOnClickListener {
            showListPopUpWindow()
        }
        llPurchasedFrom.setOnClickListener {
            //showListPopUpWindowPurchasedFrom()
            if (retailerList.size > 0) {
                retailerSpinnerDialog = RetailerSpinnerDialog(
                    context,
                    retailerList,
                    "Select or Search Channel",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, sId: String) {
                            txtPurchasedFrom.text = item
                            retailerId = sId
                        }

                    })
                retailerSpinnerDialog.showSpinerDialog()
            }

        }

        llDistributor.setOnClickListener {
            if (distributorList.size > 0) {
                distributorSpinnerDialog = DistributorSpinnerDialog(
                    context,
                    distributorList,
                    "Select or Search Retailer",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, sId: String) {
                            txtDistributor.text = item
                            distributorId = sId
                            //get retailer list
                            getRetailerApi(apiToken, distributorId)
                        }

                    })
                distributorSpinnerDialog.showSpinerDialog()
            }
        }

    }

    private fun showListPopUpWindow() {
        val adapter = ArrayAdapter<String>(context, android.R.layout.simple_list_item_1, arrayList)
        var listPopUpWindow: ListPopupWindow = ListPopupWindow(context)
        listPopUpWindow.setOnItemClickListener(AdapterView.OnItemClickListener { parent, view, position, id ->
            txtPurchasedArea.text = arrayList.get(position)
            listPopUpWindow.dismiss()
        })
        listPopUpWindow.anchorView = llPurchasedArea
        listPopUpWindow.setAdapter(adapter)
        listPopUpWindow.show()
    }

    private fun showListPopUpWindowPurchasedFrom() {
        val adapter =
            ArrayAdapter<String>(context, android.R.layout.simple_list_item_1, listPurchasedFrom)
        var listPopUpWindow: ListPopupWindow = ListPopupWindow(context)
        listPopUpWindow.setOnItemClickListener(AdapterView.OnItemClickListener { parent, view, position, id ->
            txtPurchasedFrom.text = listPurchasedFrom.get(position)
            listPopUpWindow.dismiss()
        })
        listPopUpWindow.anchorView = llPurchasedFrom
        listPopUpWindow.setAdapter(adapter)
        listPopUpWindow.show()
    }

    //get sold lead
    private fun soldLead(purchaseFrom: String, pArea: String, leadStatus: String) {
        progressBar.visibility = View.VISIBLE
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", apiToken)
        query.put("status", leadStatus)
        query.put("lead_id", leadId)
        query.put("purchased_from", retailerId)
        query.put("purchased_area", pArea)
        query.put("distributor_id", distributorId)

        val gson = Gson()
        val json = gson.toJson(query)

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getUpdateLeadStatus(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CloseLeadResponse>() {
                override fun onCompleted() {
                    progressBar.visibility = View.GONE
                    dismiss()
                }

                override fun onError(e: Throwable) {
                    progressBar.visibility = View.GONE
                    dismiss()
                }

                override fun onNext(closeLead: CloseLeadResponse) {
                    progressBar.visibility = View.GONE
                    if (closeLead.status_code == 200) {
                        var intent = Intent(Constants.REFRESH)
                        intent.putExtra("isRefresh", "yes")
                        LocalBroadcastManager.getInstance(context).sendBroadcast(intent)

                        dismiss()
                    }

                }
            }
            )
    }

    //get distributorList
    private fun getDistributorApi(token: String) {
        progressBar.visibility = View.VISIBLE

        val query = java.util.HashMap<String, Any>()
        // query.put("ECN", ecn)
        query.put("apiToken", token)
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getDistributorList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DistributorResponse>() {
                override fun onCompleted() {
                    progressBar.visibility = View.GONE

                }

                override fun onError(e: Throwable) {
                    progressBar.visibility = View.GONE
                    //  Toast.makeText(context,e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.",Toast.LENGTH_LONG).show()
                }

                override fun onNext(distributoeBean: DistributorResponse) {
                    progressBar.visibility = View.GONE

                    if (!distributoeBean.data.isNullOrEmpty()) {
                        if (distributoeBean.status_code == 200) {
                            distributorList.clear()
                            distributorList.addAll(distributoeBean.data)
                        }
                    }

                }
            }
            )
    }

    //end
    //get distributorList
    private fun getRetailerApi(token: String, distributorId: String) {
        progressBar.visibility = View.VISIBLE
        val query = java.util.HashMap<String, Any>()
        // query.put("ECN", ecn)
        query.put("apiToken", token)
        query.put("distributor_id", distributorId)

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getRetailerResponse(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<RetailerResponse>() {
                override fun onCompleted() {
                    progressBar.visibility = View.GONE
                }

                override fun onError(e: Throwable) {
                    progressBar.visibility = View.GONE

                }

                override fun onNext(retailerBean: RetailerResponse) {
                    progressBar.visibility = View.GONE
                    if (!retailerBean.data.isNullOrEmpty()) {
                        if (retailerBean.status_code == 200) {
                            retailerList.clear()
                            retailerList.addAll(retailerBean.data)
                        }
                    }

                }
            }
            )
    }
    //end

    fun validateForm(): Boolean {
        distributor = txtDistributor.text.toString()
        purchaseFrom = txtPurchasedFrom.text.toString()
        purchaseArea = txtPurchasedArea.text.toString()
        if (distributor.equals("")) {
            Toast.makeText(context, "Please select channel", Toast.LENGTH_LONG).show()
            return false
        } else if (purchaseFrom.equals("")) {
            Toast.makeText(context, "Please select retailer", Toast.LENGTH_LONG).show()
            return false
        } else if (purchaseArea.equals("")) {
            Toast.makeText(context, "Please select purchased area", Toast.LENGTH_LONG).show()
            return false
        }

        return true

    }

    //show dialog
    fun surityDialog(): Unit {
        val dialog = Dialog(context)
        dialog.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        dialog.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        dialog.setContentView(R.layout.dialog_for_ask_surity)
        dialog.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )

        dialog.window!!.setBackgroundDrawableResource(android.R.color.transparent)
        dialog.txtNo.setOnClickListener {
            dialog.dismiss()

        }
        dialog.txtYes.setOnClickListener {
            soldLead(purchaseFrom, "" + purchaseArea, "3")
            dialog.dismiss()
        }



        dialog.show()
    }

}