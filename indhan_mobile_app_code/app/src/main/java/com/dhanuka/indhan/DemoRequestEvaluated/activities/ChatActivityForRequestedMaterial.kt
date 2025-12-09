package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.os.Bundle
import android.os.Handler
import android.text.TextUtils
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.ChatAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.ChatListData
import com.dhanuka.indhan.DemoRequestEvaluated.models.ChatListResponse
import com.dhanuka.indhan.DemoRequestEvaluated.models.MessageItemUi
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponseData
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.chat_activity.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.ArrayList


class ChatActivityForRequestedMaterial : BaseActivity(), SwipeRefreshLayout.OnRefreshListener {
    private var requestedData: RequestedMaterialResponseData? = null
    private lateinit var preference: Preference
    private var appDataBaseClient: AppDatabase? = null
    private var chatAdapter: ChatAdapter? = null
    private var chatListData: ArrayList<ChatListData> = ArrayList()
    private var mainMessageList: ArrayList<MessageItemUi> = ArrayList()
    private var ecn: String = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.send_image -> {
                sendMessage()
            }
            R.id.swipeRefreshLayout -> swipeRefreshLayout.post {
                swipeRefreshLayout.isRefreshing = true
            }
        }
    }

    private fun sendMessage() {
        if (!TextUtils.isEmpty(chat_input_msg.text.toString().trim())) {
            val messageData = MessageItemUi(
                content = chat_input_msg.text.toString().trim(),
                messageType = 0,
                date = Utility.getDateCurrent(DateTime(), "yyyy-MM-dd HH:mm:ss") ?: "",
                name = ""
            )
            mainMessageList.add(0, messageData)
            chat_input_msg.setText("")
            if (chatAdapter == null) {
                chatAdapter = ChatAdapter(mainMessageList)
                recyclerViewChat.adapter = chatAdapter
                Handler(mainLooper).postDelayed({
                    checkToScroll()
                }, 200)
            } else {
                chatAdapter?.notifyDataSetChanged()
                Handler(mainLooper).postDelayed({
                    checkToScroll()
                }, 200)
            }

            callApiSendMessageToServer(messageData)
        } else {
            makeToast("Please enter some message to send.")
        }
    }

    private fun callApiSendMessageToServer(messageData: MessageItemUi) {
        val hashMap: HashMap<String, Any> = HashMap()
        hashMap["apiToken"] = preference.getString("token")
        hashMap["content"] = messageData.content
        hashMap["demo_material_request_id"] = requestedData?.demo_material_request_id ?: 0
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .sendChatMessage(hashMap).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                    chat_input_msg.setText("")
                    hideProgress()
                }

                override fun onError(e: Throwable?) {
                    chat_input_msg.setText("")
                    hideProgress()
                }

                override fun onNext(it: CommonAtpResponse?) {
                    chat_input_msg.setText("")
                    hideProgress()
                }

            })
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.chat_activity)
        preference = Preference(this)
        ecn = preference.getString("ecn")
        appDataBaseClient = DatabaseClient.getInstance(this).appDatabase
        requestedData = Gson().fromJson(
            intent?.getStringExtra("requested_data").toString(),
            RequestedMaterialResponseData::class.java
        )
        setListener()
        setAdapter()
        getChatDataFromDb()
        getChatMessages()
    }

    override fun onRefresh() {
        swipeRefreshLayout.isRefreshing = true
        getChatMessages()
    }

    private fun setListener() {
        swipeRefreshLayout.setOnRefreshListener(this)
        send_image.setOnClickListener(this)
    }

    private fun getChatMessages() {
        if (CheckConnectionStatus.isInternetAvailable(this@ChatActivityForRequestedMaterial)) {
            showProgress()
            var time = "0"
            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    getLastHitTimeData("v3/listQueryRequestedMaterial")?.let {
                        time = it.updatedAt ?: "0"
                    }
                }.run {
                    withContext(Dispatchers.Main) {
                        val hashMap: HashMap<String, Any> = HashMap()
                        hashMap["apiToken"] = preference.getString("token")
                        hashMap["updated_date"] = time
                        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                            .getChatListData(hashMap).subscribeOn(Schedulers.newThread())
                            .observeOn(AndroidSchedulers.mainThread())
                            .subscribe(object : Subscriber<ChatListResponse>() {
                                override fun onCompleted() {
                                    swipeRefreshLayout.isRefreshing = false
                                    hideProgress()
                                }

                                override fun onError(e: Throwable?) {
                                    swipeRefreshLayout.isRefreshing = false
                                    hideProgress()
                                }

                                override fun onNext(it: ChatListResponse?) {
                                    swipeRefreshLayout.isRefreshing = false
                                    hideProgress()
                                    it?.let {
                                        if (it.status_code == 200) {
                                            insertChatDataInDb(it.data)
                                        } else if (it.status_code == 409) {
                                            EventBus.getDefault().post("logout")
                                        }
                                    }
                                }

                            })
                    }
                }
            }
        } else {
            swipeRefreshLayout.isRefreshing = false
            hideProgress()
            getChatDataFromDb()
        }
    }

    private fun insertChatDataInDb(data: List<ChatListData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach() {
                    appDataBaseClient?.chatListDataDao()?.insert(it)
                }
            }.run {
                delay(400)
                withContext(Dispatchers.Main) {
                    "v3/listQueryRequestedMaterial".updateTimeOnDb()
                    getChatDataFromDb()
                }
            }
        }
    }

    private fun getChatDataFromDb() {
        chatListData.clear()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                chatListData = (appDataBaseClient?.chatListDataDao()
                    ?.getAllMessagesData(requestedData?.demo_material_request_id ?: 0)
                    ?: ArrayList<ChatListData>()) as ArrayList<ChatListData>
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun setAdapter() {
        mainMessageList.clear()
        for ((index) in chatListData.withIndex()) {
            val it = chatListData[index]
            val type = if (it.user_ecn == ecn) {
                0
            } else {
                1
            }

            val messageData = MessageItemUi(
                content = it.content,
                messageType = type,
                date = it.updated_at,
                name = ("Name ".plus(it.user_name)).plus(
                    if (!TextUtils.isEmpty(it.user_ecn)) ("\nECN - ").plus(it.user_ecn) else ""
                )
            )
            mainMessageList.add(messageData)
        }
        mainMessageList.reverse()
        if (chatAdapter == null) {
            chatAdapter = ChatAdapter(mainMessageList)
            recyclerViewChat.adapter = chatAdapter
            Handler(mainLooper).postDelayed({
                checkToScroll()
            }, 200)
        } else {
            chatAdapter?.notifyDataSetChanged()
            Handler(mainLooper).postDelayed({
                checkToScroll()
            }, 200)
        }

    }

    private fun checkToScroll() {
        val layoutManager = LinearLayoutManager::class.java.cast(recyclerViewChat.layoutManager)
        val totalItemCount = layoutManager?.itemCount ?: 0
        val lastVisible = layoutManager?.findLastVisibleItemPosition() ?: 0
        val endHasBeenReached = lastVisible >= totalItemCount
        if (totalItemCount <= 0 || endHasBeenReached) {
            recyclerViewChat.smoothScrollToPosition(0)
        }
    }

    private fun getLastHitTimeData(apiName: String): ApiTimeTableModel? {
        return appDataBaseClient?.apiTimeDao()
            ?.getApiTimeData(apiName, (requestedData?.demo_material_request_id ?: 0).toString())
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = getLastHitTimeData(this@updateTimeOnDb)
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDataBaseClient?.apiTimeDao()
                        ?.updateTimeInDb(
                            this@updateTimeOnDb,
                            currentDate,
                            (requestedData?.demo_material_request_id ?: 0).toString()
                        )
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    timeDataModel.typeId = (requestedData?.demo_material_request_id ?: 0).toString()
                    appDataBaseClient?.apiTimeDao()?.insert(timeDataModel)
                }
            }
        }
    }
}