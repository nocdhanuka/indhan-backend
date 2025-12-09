package com.dhanuka.indhan.myteam

import android.app.Activity
import android.app.Dialog
import android.content.Context
import android.os.Bundle
import android.view.LayoutInflater
import android.view.ViewGroup
import android.view.Window
import android.view.WindowManager
import androidx.appcompat.app.AlertDialog.Builder
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictBean
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.StateBean
import com.dhanuka.indhan.Activity.NewTicketMVP.StateSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilBean
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilModel
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilSpinnerDialog
import com.dhanuka.indhan.BuildConfig
import com.dhanuka.indhan.PlanATP.Model.VillageListTehsil
import com.dhanuka.indhan.PlanATP.TehsilVillageSpinnerDialog
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.View.CustomProgressDialog
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import kotlinx.android.synthetic.main.dialog_village_assignee.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.set


class VillageAssignDialog(context: Context, listner: OnGetId) : Dialog(context) {
    private lateinit var preference: Preference
    lateinit var reason: String
    private lateinit var villageSpinner: TehsilVillageSpinnerDialog
    private lateinit var stateSpinner: StateSpinnerDialog
    private lateinit var districSpinner: DistrictSpinnerDialog
    private lateinit var tehsilSpinner: TehsilSpinnerDialog
    private lateinit var listner: OnGetId

    private var state_id = 0
    private var state = ""
    private var district_id = ""
    private var district = ""
    private var tehsil_id = 0
    private var village = ""
    private lateinit var activity: Activity

    lateinit var progressDialog: CustomProgressDialog

    private var stateModelLis: ArrayList<StateBean> = ArrayList()
    private var tehsilList: ArrayList<TehsilBean> = ArrayList()
    private var villageList: ArrayList<VillageListTehsil.Data> = ArrayList()
    private var districtList: ArrayList<DistrictBean> = ArrayList()

    init {
        activity = context as Activity
        this.listner = listner
        progressDialog = CustomProgressDialog(activity)


    }


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        this.requestWindowFeature(Window.FEATURE_NO_TITLE)
        this.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        //this.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        setContentView(LayoutInflater.from(context).inflate(R.layout.dialog_village_assignee, null))
        this.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )



        this.window!!.setBackgroundDrawableResource(android.R.color.transparent)

        preference = Preference(context)
        state_id = preference.getInt("state_id")
        state = preference.getString("state")

        spinner_state.text = state

        getDistrict(state_id.toString())
        spinner_state.setOnClickListener {
            stateSpinner.showSpinerDialog()

        }
        spinner_district.setOnClickListener {
            districSpinner.showSpinerDialog()

        }
        spinner_tehsil.setOnClickListener {
            tehsilSpinner.showSpinerDialog()

        }
        etRetVillage.setOnClickListener {
            villageSpinner.showSpinerDialog()

        }

        btnSubmit.setOnClickListener {
            if (village != "") {
                alertDialog()

            }
        }




        stateSpinner = StateSpinnerDialog(activity, stateModelLis, "Select State")
        districSpinner = DistrictSpinnerDialog(activity, districtList, "Select District")
        tehsilSpinner = TehsilSpinnerDialog(activity, tehsilList, "Select Tehsil")
        villageSpinner = TehsilVillageSpinnerDialog(activity, villageList, "Select Village")

        districSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                district_id = districtList[position].getdID().toString()
                spinner_district.text = districtList[position].item
                gettehsil(district_id)

                spinner_tehsil.text = ""
                tehsilList.clear()
                etRetVillage.text = ""
                villageList.clear()


            }
        })

        villageSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                village = villageList[position].villageId
                etRetVillage.text = villageList[position].village
            }
        })

        stateSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                state = item
                state_id = stateModelLis.get(position).getsID()!!
                spinner_state.text = item


            }
        })

        tehsilSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_tehsil.text = item
                if (CheckConnectionStatus.isInternetAvailable(context)) {
                    getVillageList(tehsilList[position].gettID().toString())
                } else {
                    //context.makeToast(resources.getString(R.string.no_internet_connection))
                }
                etRetVillage.text = ""
                villageList.clear()

            }
        })


    }


    //reason for active atp demo api

    private fun getVillageList(tehsilID: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["tehsil_id"] = tehsilID


        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).tehshilVillageList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<VillageListTehsil>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    dismissProgress()
                    //makeToast(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                    logEventCustom(
                        e.localizedMessage ?: "Custom Message -> error is null",
                        "v2/tehshilVillageList"
                    )
                }

                override fun onNext(villageBean: VillageListTehsil) {
                    dismissProgress()
                    villageList.clear()
                    villageList.addAll(villageBean.data)
                    villageSpinner.updatelist(villageBean.data)
                    if (villageBean.statusCode != 200) {
                        logEventCustom(
                            villageBean.message,
                            "v2/tehshilVillageList"
                        )
                    }
                }
            })
    }


    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = apiName
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        Utility.logEvent("VillageAssignDialog", "failed_api_$apiName", bundle)
    }

    private fun getDistrict(stateID: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query.put("apiToken", preference.getString("token"))
        query.put("sId", stateID)
        query.put("app_version", BuildConfig.VERSION_NAME)
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userDistrict(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DistrictModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    dismissProgress()
                    //listener.onDistrictFailed("Not able  to connect")
                }

                override fun onNext(districtBean: DistrictModel) {
                    dismissProgress()

                    if (districtBean.status_code == 200) {
                        spinner_tehsil.setText("Select Tehsil")
                        tehsil_id = 0
                        districtList.clear()
                        districtList.addAll(districtBean.data!!)
                        districSpinner.updatelist(districtList)
//                            districtAdapter.notifyDataSetChanged()
//                            districtSpinnerDialog = DistrictSpinnerDialog(this, districtRecordResponseModel, "Select or Search District")
//                            districtSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                                override fun onClick(item: String, position: Int) {
//                                    spinnerDistrict.text = item
//                                    districtId = districtRecordResponseModel[position].getdID()!!
//                                    districtName = districtRecordResponseModel[position].item.toString()
//                                    if (districtId != 0) {
//                                        val query = HashMap<String, Any>()
//                                        query.put("apiToken", Token)
//                                        query.put("dId", districtId)
//                                        if (CheckConnectionStatus.isInternetAvailable(this@NewTicketActivity)) {
//                                            mtehsilPresenter.onSubmitClick(query)
//                                        } else {
//                                            makeToast(resources.getString(R.string.no_internet_connection))
//                                        }
//                                    }
//                                }
//                            })
                        tehsilList.clear()
                        /*tehsilRecordResponseModel.add(0, tehsilBean)
                        tehsilBean.settID(0)
                        tehsilBean.item = "Select Tehsil"*/
//                            te.notifyDataSetChanged()
                    }
                }
            })
    }


    private fun gettehsil(districtId: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query.put("apiToken", preference.getString("token"))
        query.put("dId", districtId)
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userTehsil(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TehsilModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    dismissProgress()
//                        listener.onTehsilFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(tehsilBean: TehsilModel) {
                    dismissProgress()
                    if (tehsilBean.status_code == 200) {
                        tehsilList.clear()

                        spinner_tehsil.setText("Select Tehsil")
                        tehsilList.addAll(tehsilBean.data!!)
                        tehsilSpinner.updatelist(tehsilList)
                        villageList.clear()
                        villageSpinner.updatelist(villageList)
//                            tehsilSpinnerDialog = TehsilSpinnerDialog(this, tehsilRecordResponseModel, "Select or Search Tehsil")
//                            tehsilSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
//                                override fun onClick(item: String, position: Int) {
//                                    spinnerTehsil.text = item
//                                    tehsilId = tehsilRecordResponseModel[position].gettID()!!
//                                    tehsilName = tehsilRecordResponseModel[position].item.toString()
//                                    val query = HashMap<String, Any>()
//                                    query["tehsil_id"] = tehsilId.toString()
//                                    query["apiToken"] = preference.getString("token")
//
//                                    if (CheckConnectionStatus.isInternetAvailable(this@NewTicketActivity)) {
//                                        msubmitTicketPresenter.onVillageList(query)
//                                    } else {
//                                        makeToast(resources.getString(R.string.no_internet_connection))
//                                    }
//                                }
//                            })
                    }
                }
            })
    }


    fun showProgress() {
        if (progressDialog == null) {
            progressDialog = CustomProgressDialog(context)
            progressDialog.show()
        } else {
            if (!progressDialog.isShowing()) {
                progressDialog.show()
            }
        }
    }

    fun dismissProgress() {
        if (!activity.isFinishing && progressDialog != null &&
            progressDialog.isShowing
        ) {
            progressDialog.dismiss()
        }
    }

    private fun alertDialog() {
        Builder(activity).setTitle("Assign Village")
            .setMessage("Are you sure you want to assign village?")
            .setPositiveButton("Yes") { dialogInterface, i ->
                listner.onClick(etRetVillage.text.toString(), 0, village)
                dismiss()

            }.setNegativeButton("No") { dialogInterface, i ->
                dialogInterface.dismiss()
            }.show()
    }


}