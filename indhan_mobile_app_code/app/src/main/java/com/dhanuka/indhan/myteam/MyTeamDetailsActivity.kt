package com.dhanuka.indhan.myteam

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.bumptech.glide.Glide
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.SessionNameDialog
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.TestimonialList.filter.DDBean
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.validatefarmers.SuccessBean
import kotlinx.android.synthetic.main.activity_my_team_details.*
import kotlinx.android.synthetic.main.activity_profile.imgProfile
import kotlinx.android.synthetic.main.activity_profile.txtAltPhoneNo
import kotlinx.android.synthetic.main.activity_profile.txtEmail
import kotlinx.android.synthetic.main.activity_profile.txtID
import kotlinx.android.synthetic.main.activity_profile.txtPhoneNo
import kotlinx.android.synthetic.main.activity_profile.txtUserName
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class MyTeamDetailsActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }


    private lateinit var data: DDBean.Data
    private lateinit var preference: Preference
    private lateinit var profileData: DDProfileDetails

    private lateinit var ddAdapter: DDHistoryAdapter
    private lateinit var sessionNameDialog: SessionNameDialog
    private lateinit var villagesHistoryAdapter: VillagesHistoryAdapter

    var villageList: ArrayList<AllVillageBean.Data> = ArrayList()
    var sessionList: ArrayList<SessionBean.Data> = ArrayList()

    private var villageId = ""
    private var sessionID = ""
    private var ecn = ""
    private var ddID = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_team_details)
        preference = Preference(this)
        if (intent.hasExtra("ecn")) {
            ecn = intent?.getStringExtra("ecn") ?: ""
            txtSelectVillage.isEnabled = false
            txtSelectSession.isEnabled = false
        } else {
            data = intent.getSerializableExtra("data") as DDBean.Data
            ecn = data.eCN
            ddID = data.ddId
        }
        getMyTeamDetailsList()
        getSessionList(preference.getString("token"))
        showProgress()

        txtPhoneNo.setOnClickListener {
            val intent = Intent(Intent.ACTION_DIAL)
            intent.data = Uri.parse("tel: ${profileData.data.ddMobileNo}")
            startActivity(intent)
        }

        txtAltPhoneNo.setOnClickListener {
            val intent = Intent(Intent.ACTION_DIAL)
            intent.data = Uri.parse("tel: ${profileData.data.ddAltMobileNo}")
            startActivity(intent)

        }

        txtSelectVillage.setOnClickListener {
            openVillageAssige()
        }

        txtSelectSession.setOnClickListener {
            if (sessionList.size > 0) {
                sessionNameDialog = SessionNameDialog(
                    this,
                    sessionList,
                    "Select or Search Village",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, sId: String) {
                            sessionID = sId
                            txtSelectSession.text = item
                            addSessionList()

                        }

                    })
                sessionNameDialog.showSpinerDialog()
            }
        }


    }


    /*
    * get team detaisl or profile
    * */

    /*
    * get DD list
    * */
    private fun getMyTeamDetailsList() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["ECN"] = ecn


        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .ddBDOprofilesummary(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DDProfileDetails>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: DDProfileDetails) {
                    hideProgress()
                    if (listBean.statusCode == 200) {
                        profileData = listBean
                        setData(listBean.data)
                    }


                }
            })

    }


    private fun addSessionList() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["dd_id"] = ddID
        query["season_id"] = sessionID







        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).addDdSeason(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: SuccessBean) {
                    hideProgress()
                    makeToast(listBean.message)


                }
            })

    }


    private fun addVillageList() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["dd_id"] = data.ddId
        query["village_id"] = villageId

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).addVillage(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: SuccessBean) {
                    hideProgress()
                    makeToast(listBean.message)
                    getMyTeamDetailsList()


                }
            })

    }


    /*
    * set data
    * */
    private fun setData(data: DDProfileDetails.Data) {
        ddID = data.ddId
        txtUserName.text = data.name
        txtID.text = "ID: " + data.ddCode
        txtPhoneNo.text = data.ddMobileNo
        txtAltPhoneNo.text = data.ddAltMobileNo
        txtEmail.text = data.ddEmail
        txtSession.text = data.dd_season
        txtSession.let {
            if (data.dd_season == "") {
                it.text = "No session selected "
            } else {
                it.text = data.dd_season
            }
        }
        if (data.ddImage != null) {
            Glide.with(this)
                .load(data.ddImage)
                .apply(RequestOptions().placeholder(R.drawable.def_user).error(R.drawable.def_user))
                .into(imgProfile)
        }

        /*
        * dd history adapter
        * */
        ddAdapter = DDHistoryAdapter(this, data.ddHistory)
        recyclerViewDD.layoutManager =
            LinearLayoutManager(this)
        recyclerViewDD.adapter = ddAdapter

        /*
        * village history adapter
        * */
        villagesHistoryAdapter = VillagesHistoryAdapter(this, data.myVillages)
        recyclerViewVillage.layoutManager =
            LinearLayoutManager(this)
        recyclerViewVillage.adapter = villagesHistoryAdapter
    }

    private fun getSessionList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = token
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).allSeasonList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SessionBean>() {
                override fun onCompleted() {
                    hideProgress()

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(sessionBean: SessionBean) {
                    hideProgress()
                    if (!sessionBean.data.isNullOrEmpty()) {
                        sessionList.clear()
                        sessionList.addAll(sessionBean.data)
                    }

                }
            }
            )

    }


    /**
     * open dialog for
     *
     * */
    private fun openVillageAssige() {
        val s = VillageAssignDialog(this@MyTeamDetailsActivity, object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                villageId = sId
                txtSelectedVillage.text = item
                addVillageList()
            }
        })
        s.show()
    }
}

