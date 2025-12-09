package com.dhanuka.indhan.Activity

import android.content.Intent
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.DefaultItemAnimator
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Model.AddNumberModel
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_add_numbers.*
import java.io.Serializable
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.LinkedHashSet

/**
 * Created by Shivam on 3/8/2018.
 */

class AddNumberActivity : BaseActivity(), AddNumberClickListener {

    private val mAddNumberModelList = ArrayList<AddNumberModel>()
    private lateinit var mAdapter: AddNumberAdapter
    internal lateinit var simpleDateFormat: SimpleDateFormat
    private lateinit var serverDateFormat: SimpleDateFormat
    private val uniqueNumberRecordResponseModel = ArrayList<AddNumberModel>()
    private var linkedhashSet = LinkedHashSet<AddNumberModel>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_numbers)
        tvReAddNewPhone.setOnClickListener(this)
        ivSaveNumbers.setOnClickListener(this)
        ivCross.setOnClickListener(this)

        val mLayoutManager =
            LinearLayoutManager(this)
        rvListPhoneCall.layoutManager = mLayoutManager
        rvListPhoneCall.itemAnimator =
            DefaultItemAnimator()

        mAdapter = AddNumberAdapter(mAddNumberModelList, this)
        rvListPhoneCall.adapter = mAdapter
        mAdapter.setClickListener(this)
        addData()
    }

    fun getLocalTime(): String {
        val calander = Calendar.getInstance( Locale.ENGLISH)
        simpleDateFormat = SimpleDateFormat("hh:mm a", Locale.ENGLISH)
        return simpleDateFormat.format(calander.time)
    }

    fun getServerTime(): String {
        val calander = Calendar.getInstance( Locale.ENGLISH)
        serverDateFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.ENGLISH)
        return serverDateFormat.format(calander.time)
    }

    private fun createEmptyAddNumberModel(): AddNumberModel {
        val pseudoNumber = ""
        val addNumberModel = AddNumberModel()
        addNumberModel.number = pseudoNumber
        addNumberModel.time = getLocalTime()
        addNumberModel.serverTime = getServerTime()
        addNumberModel.status = 1
        return addNumberModel
    }

    fun addData() {
        if (mAddNumberModelList.size == 0) {
            mAddNumberModelList.add(0, createEmptyAddNumberModel())
        }
        mAdapter.notifyItemChanged(mAddNumberModelList.size)
    }

    override fun onClick(v: View) {
        when (v.id) {
            R.id.tvReAddNewPhone -> {
                var someIndexIsEmpty = false
                for (indexArray in mAddNumberModelList.indices) {
                    if (mAddNumberModelList[indexArray].number.trim().isEmpty()) {
                        makeToast("Please enter the Phone Number")
                        someIndexIsEmpty = true
                        break
                    }
                    if (mAddNumberModelList[indexArray].number.trim().length != 10) {
                        makeToast("Please enter correct Phone Number")
                        someIndexIsEmpty = true
                        break
                    }
                }
                if (!someIndexIsEmpty) {
                    mAddNumberModelList.add(mAddNumberModelList.size, createEmptyAddNumberModel())
                }
                mAdapter.notifyItemChanged(mAddNumberModelList.size)
            }

            R.id.ivSaveNumbers -> {
                var allGoneWell = false
                val newRecordResponseModelList = ArrayList<AddNumberModel>()
                for (indexOfArray in mAddNumberModelList.indices) {
                    if (!mAddNumberModelList[indexOfArray].number.trim().isEmpty()) {
                        newRecordResponseModelList.add(mAddNumberModelList[indexOfArray])
                    }
                }

                if (newRecordResponseModelList.size == 0) {
                    makeToast("Please add at least 1 Phone number")
                } else if (newRecordResponseModelList.size != 0) {
                    for (indexOfArray in newRecordResponseModelList.indices) {
                        if (newRecordResponseModelList[indexOfArray].number.length != 10) {
                            if (newRecordResponseModelList[indexOfArray].number.trim().isEmpty()) {
                            } else {
                                allGoneWell = false
                                makeToast("Phone number length should be 10.")
                                break
                            }
                        }
                        allGoneWell = true
                    }
                }
                if (allGoneWell) {
                    linkedhashSet.clear()
                    for (indexOfArray in newRecordResponseModelList.indices) {
                        val itr = linkedhashSet.iterator()
                        var isNumberAlreadyInList = false
                        while (itr.hasNext()) {
                            if (itr.next().number.trim() == newRecordResponseModelList[indexOfArray].number.trim()) {
                                isNumberAlreadyInList = true
                            }
                        }
                        if (!isNumberAlreadyInList)
                            linkedhashSet.add(newRecordResponseModelList[indexOfArray])
                    }
                    uniqueNumberRecordResponseModel.clear()
                    uniqueNumberRecordResponseModel.addAll(0, linkedhashSet)
                    val resultCode = 101
                    val resultIntent = Intent()
                    resultIntent.putExtra(
                        "UNIQUE_NUMBERS",
                        uniqueNumberRecordResponseModel as Serializable
                    )
                    setResult(resultCode, resultIntent)
                    finish()
                }
            }
            R.id.ivCross -> finish()
        }
    }

    override fun onClick(view: View, addNumberModel: AddNumberModel, position: Int) {}
}
