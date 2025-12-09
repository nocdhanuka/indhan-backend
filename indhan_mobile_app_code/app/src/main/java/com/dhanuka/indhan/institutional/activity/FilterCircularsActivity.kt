package com.dhanuka.indhan.institutional.activity

import android.content.Intent
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.SelectDateFragment
import kotlinx.android.synthetic.main.activity_filter_circulars.*

class FilterCircularsActivity : BaseActivity(), SelectDateFragment.myDialogListener {

    private var fromDateCreatedString: String = ""
    private var toDateCreatedString: String = ""
    private var fromDatePublishedString: String = ""
    private var toDatePublishedString: String = ""


    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.reset -> {
                resetEverything()
            }
            R.id.apply -> {
                proceedApply()
            }
            R.id.fromDate -> {
                fromDateCreatedString = ""
                launchDateicker(2)
            }
            R.id.toDate -> {
                if (fromDateCreatedString.isNotEmpty()) {
                    toDateCreatedString = ""
                    launchDateicker(3)
                } else
                    makeToast("Please select created from date")
            }
            R.id.fromDatePublished -> {
                fromDatePublishedString = ""
                launchDateicker(4)
            }
            R.id.toDatePublished -> {
                if (fromDatePublishedString.isNotEmpty()) {
                    toDatePublishedString = ""
                    launchDateicker(5)
                } else
                    makeToast("Please select published from date")

            }
        }
    }

    private fun setFilterData() {
        intent.getBundleExtra("bundle")?.let {
            fromDateCreatedString = it.getString("created_from") ?: ""
            toDateCreatedString = it.getString("created_to") ?: ""
            fromDatePublishedString = it.getString("published_from") ?: ""
            toDatePublishedString = it.getString("published_to") ?: ""

            if (!TextUtils.isEmpty(fromDateCreatedString)) {
                fromDate.text = fromDateCreatedString
            }
            if (!TextUtils.isEmpty(toDateCreatedString)) {
                toDate.text = toDateCreatedString
            }
            if (!TextUtils.isEmpty(fromDatePublishedString)) {
                fromDatePublished.text = fromDatePublishedString
            }
            if (!TextUtils.isEmpty(toDatePublishedString)) {
                toDatePublished.text = toDatePublishedString
            }
        }
    }

    private fun proceedApply() {
        val intent = Intent()
        val b = Bundle()
        b.putString("created_from", fromDateCreatedString)
        b.putString("created_to", toDateCreatedString)
        b.putString("published_from", fromDatePublishedString)
        b.putString("published_to", toDatePublishedString)
        intent.putExtra("bundle", b)
        setResult(RESULT_OK, intent)
        finish()
    }

    private fun resetEverything() {
        toDateCreatedString = ""
        toDate.text = toDateCreatedString
        toDatePublishedString = ""
        toDatePublished.text = toDatePublishedString
        fromDateCreatedString = ""
        fromDate.text = fromDateCreatedString
        fromDatePublishedString = ""
        fromDatePublished.text = fromDatePublishedString
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_filter_circulars)
        setFilterData()
        setListeners()
    }

    private fun setListeners() {
        iCross.setOnClickListener(this)
        apply.setOnClickListener(this)
        fromDate.setOnClickListener(this)
        fromDatePublished.setOnClickListener(this)
        toDate.setOnClickListener(this)
        toDatePublished.setOnClickListener(this)
        reset.setOnClickListener(this)
    }

    private fun launchDateicker(i: Int) {
        val bundle = Bundle()
        bundle.putInt("key", i)
        val newFragment = SelectDateFragment()
        newFragment.arguments = bundle
        newFragment.show(this@FilterCircularsActivity.supportFragmentManager, "DatePicker")
    }

    override fun onStartDateSelected(startDate: String?, value: Int) {
        if (value == 2) {
            fromDateCreatedString = startDate!!
            fromDate.text = fromDateCreatedString
        } else if (value == 4) {
            fromDatePublishedString = startDate!!
            fromDatePublished.text = fromDatePublishedString
        }
    }

    override fun onEndDateSelected(startDate: String?, value: Int) {
        if (value == 3) {
            toDateCreatedString = startDate!!
            toDate.text = toDateCreatedString
        } else if (value == 5) {
            toDatePublishedString = startDate!!
            toDatePublished.text = toDatePublishedString
        }
    }
}