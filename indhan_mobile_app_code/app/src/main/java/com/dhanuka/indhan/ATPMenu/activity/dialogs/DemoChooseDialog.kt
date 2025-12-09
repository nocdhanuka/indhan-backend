package com.dhanuka.indhan.ATPMenu.activity.dialogs

import android.app.Dialog
import android.content.Context
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.*
import android.widget.Toast
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.adapters.DemoChooseAdapter
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestData
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestListResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSuccess
import kotlinx.android.synthetic.main.dialog_choose_demo.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers


class DemoChooseDialog(context: Context, planId: String, onSuccess: OnSuccess) : Dialog(context) {
    private lateinit var preference: Preference
    var reason: String = ""
    private var adapter: DemoChooseAdapter? = null
    private var demoRequestList: ArrayList<DemoRequestData> = ArrayList()
    var demoRequestId: String = ""
    var planId: String
    var listener: OnSuccess

    init {
        this.planId = planId
        this.listener = onSuccess
    }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        this.requestWindowFeature(Window.FEATURE_NO_TITLE)
        this.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        //this.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        setContentView(LayoutInflater.from(context).inflate(R.layout.dialog_choose_demo, null))
        this.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.MATCH_PARENT
        )



        this.window!!.setBackgroundDrawableResource(android.R.color.transparent)

        preference = Preference(context)
        getDemoRequestListApi()
        //apply click for activate demo
        txtDemoActivate.setOnClickListener {
            if (!demoRequestId.equals("")) {
                activateDemo(planId, demoRequestId, "0")

            } else {
                Toast.makeText(context, "Please select demo no", Toast.LENGTH_LONG).show()
            }
        }

        txtUserActivate.setOnClickListener {
            activateDemo(planId, "", "1")
        }



        searchBox.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun onTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun afterTextChanged(editable: Editable) {
                adapter?.let {
                    it.filter.filter(searchBox.text.toString())
                }
            }
        })

    }

    //get demo request list
    private fun getDemoRequestListApi() {
        progressBar.visibility = View.VISIBLE
        val model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("approval", "approved")

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).demoRequestListV1(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DemoRequestListResponse>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    GlobalScope.launch {
                        withContext(Dispatchers.Main){
                            progressBar?.let {
                                it.visibility = View.GONE
                            }

                        }
                    }
                }

                override fun onNext(demoBean: DemoRequestListResponse) {
                    progressBar.visibility = View.GONE
                    if (!demoBean.data.isNullOrEmpty()) {
                        demoRequestList.clear()
                        demoRequestList.addAll(demoBean.data)
                        //set data on adapter
                        setDataOnAdapter(demoRequestList)
                    } else {
                        txtNoItem.visibility = View.VISIBLE
                        txtDemoActivate.isEnabled = false
                        searchBox.isEnabled = false
                        txtDemoActivate.setBackgroundColor(context.resources.getColor(R.color.AppWhite))


                    }
                }
            })

        //end
    }

    //function for adapter
    private fun setDataOnAdapter(demoRequestList: ArrayList<DemoRequestData>) {
        // Creates a vertical Layout Manager
        rvDemoChoose.layoutManager =
            LinearLayoutManager(context)
        adapter = DemoChooseAdapter(context, demoRequestList, object : OnSuccess {
            override fun onClick(data: String) {
                demoRequestId = data
            }

        })
        rvDemoChoose.adapter = adapter
    }

    //reason for active atp demo api
    private fun activateDemo(planAtpId: String, demoId: String, userFramer: String) {
        progressBar.visibility = View.VISIBLE
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("plan_atp_id", planAtpId)
        query.put("demo_request_id", demoId)
        query.put("user_farmer", userFramer)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).activeDemo(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                    //(context as ActivateAtpActivity).hideProgress()
                }

                override fun onError(e: Throwable) {
                    progressBar.visibility = View.GONE

                    //(context as ActivateAtpActivity).hideProgress()
                    dismiss()
                    Toast.makeText(context,
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.",
                        Toast.LENGTH_LONG
                    ).show()
                }

                override fun onNext(commonBean: CommonAtpResponse) {
                    progressBar.visibility = View.GONE
                    if (commonBean.status_code == 200) {
                        dismiss()
                        Toast.makeText(context, commonBean.message, Toast.LENGTH_LONG).show()
                        listener.onClick("yes")

                    } else if (commonBean.status_code == 201) {
                        dismiss()
                        listener.onClick("No")
                    }
                }
            })
    }

}