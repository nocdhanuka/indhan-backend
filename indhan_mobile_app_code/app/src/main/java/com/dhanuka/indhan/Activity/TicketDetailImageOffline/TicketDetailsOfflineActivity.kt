package com.dhanuka.indhan.Activity.TicketDetailImageOffline

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import android.widget.Toast
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationDetailPresenterImpl
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationFactory
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_self_participation2.*
import okhttp3.MediaType
import okhttp3.RequestBody
import java.io.File

class TicketDetailsOfflineActivity : BaseActivity(), View.OnClickListener,
    SelfParticipationFactory.SPDetailHomeListener {
    private lateinit var adapter: TicketOfflineAdapter
    private lateinit var mActivity: TicketDetailsOfflineActivity
    private var imageOfflineModel = ArrayList<ImageOfflineModel>()
    lateinit var model: ImageOfflineModel
    var imgfile: File? = null
    var ImgUrl = ""
    var token = ""
    var ticket = 0
    lateinit var preference: Preference
    lateinit var sp_presenter: SelfParticipationFactory.SelfParticipationDetailPresenter

    override fun onClick(v: View) {
        when (v.id) {
            R.id.ivBack -> {
                onBackPressed()
            }
            R.id.ivDownload -> {
                if (CheckConnectionStatus.isInternetAvailable(mActivity)) {
                    if (imageOfflineModel.size > 0) {
                        model = imageOfflineModel[0]
                        if (model.type.equals(Constants.IS_SELF)) {

                        } else {
                            uploadTicketImage()
                        }
                    }
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_ticket_details_offline)
        mActivity = this
        preference = Preference(mActivity)
        sp_presenter = SelfParticipationDetailPresenterImpl(this)
        token = preference.getString("token")
        val extras = intent.extras
        //ticketId = extras.getInt("ticketId")
        extras?.let {
            ticket = it.getInt("tid")
        }

        ivBack.setOnClickListener(this)
        ivDownload.setOnClickListener(this)
        getOfflineData()

    }

    private fun getOfflineData() {
        val list = DatabaseManager.getNonUploadedImageOfflineTable(mActivity)
        if (list.size > 0) {
            list.forEach {
                if (it.ticket_id.equals(ticket.toString())) {
                    imageOfflineModel.add(it)
                }
            }
        }
        if (imageOfflineModel.size > 0) {
            txtNoTickets.visibility = View.GONE
            adapter = TicketOfflineAdapter(imageOfflineModel, mActivity, object : OnClickListener {
                override fun onClick(position: Int) {
                }
            })
            recyclerViewActivate.layoutManager =
                LinearLayoutManager(this)
            recyclerViewActivate.adapter = adapter
            adapter.notifyDataSetChanged()
        } else {
            txtNoTickets.visibility = View.VISIBLE
        }
    }

    private fun uploadTicketImage() {
        try {
            imgfile = File(model.image_url_local)
            val convert_File_2String = imgfile.toString()
            ImgUrl = imgfile.toString()
            val fileNAme = convert_File_2String.substring(convert_File_2String.lastIndexOf("/") + 1)
            val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
            val ticke = RequestBody.create(MediaType.parse("text/plain"), model.ticket_id)
            val token_req = RequestBody.create(MediaType.parse("text/plain"), token)
            val lat = RequestBody.create(
                MediaType.parse("text/plain"),
                preference.getString(Constants.latitude)
            )
            val long = RequestBody.create(
                MediaType.parse("text/plain"),
                preference.getString(Constants.longitude)
            )
            val date = RequestBody.create(MediaType.parse("text/plain"), model.date)
            val map: HashMap<String, RequestBody> = HashMap()
            map.put("photo\"; filename=\"" + fileNAme + "\" ", reqFile)
            map.put("api_token", token_req)
            map.put("tId", ticke)
            map.put("latitude", lat)
            map.put("longitude", long)
            map.put("updated_at", date)
            if (CheckConnectionStatus.isInternetAvailable(mActivity)) {
                if (model.isTicketOffline == 1) {
                    Toast.makeText(this, "This ticket is created Offline.", Toast.LENGTH_LONG)
                        .show()
                } else sp_presenter.onSubmitPhoto2(map)
            }
        } catch (ex: Exception) {
        }
    }

    override fun onSuccess(tst: String) {
        model.isUploaded = 1
        DatabaseManager.updateImageOfflineTable(mActivity, model)
        DatabaseManager.deleteAllImageOfflineTableParticular(this, ticket)
        imageOfflineModel.clear()
        adapter.notifyDataSetChanged()
        makeToast("Uploaded")
    }

    override fun onFailure(mes: String) {
        if (!TextUtils.isEmpty(mes)) {
            if (mes.equals("File Already Uploaded!", ignoreCase = true)) {
                DatabaseManager.deleteAllImageOfflineTableParticular(this, ticket)
                imageOfflineModel.clear()
                adapter.notifyDataSetChanged()
            }
            makeToast(mes)
        }
    }

    override fun onFailureParticipation(mes: String) {
        if (!TextUtils.isEmpty(mes)) {
            if (mes.equals("File Already Uploaded!", ignoreCase = true)) {
                DatabaseManager.deleteAllImageOfflineTableParticular(this, ticket)
                imageOfflineModel.clear()
                adapter.notifyDataSetChanged()
            }
            makeToast(mes)
        }
    }
}
