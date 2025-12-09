package com.dhanuka.indhan.institutional.adapter.influencers

import android.app.Activity
import android.content.Context
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.influencers.InfluencersAddContactModel
import com.dhanuka.indhan.institutional.model.influencers.InfluencersDesignationData
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.item_contact_detail.view.*
import java.util.*

class InfluencersAddContactAdapter(
    private val context: Context,
    private val arrayListOfData: LinkedList<InfluencersAddContactModel>
) : RecyclerView.Adapter<InfluencersAddContactAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickItem
    private var levelData: ArrayList<String> = ArrayList()
    private var selectLevel: NumberSpinner? = null
    private var selectDesignation: NumberSpinner? = null
    private var designationData: ArrayList<InfluencersDesignationData> = ArrayList()
    private var designationStringList: ArrayList<String> = ArrayList()

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            itemView.etPersonName.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {

                }

                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

                }

                override fun afterTextChanged(s: Editable?) {
                    arrayListOfData[absoluteAdapterPosition].contact_name = (s ?: "").toString()
                }
            })

            itemView.etWaNumber.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {

                }

                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

                }

                override fun afterTextChanged(s: Editable?) {
                    arrayListOfData[absoluteAdapterPosition].wa_number = (s ?: "").toString()
                }
            })

            itemView.etMobileNumber.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {

                }

                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

                }

                override fun afterTextChanged(s: Editable?) {
                    arrayListOfData[absoluteAdapterPosition].mobile_number = (s ?: "").toString()
                }
            })

            itemView.etEmail.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {

                }

                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

                }

                override fun afterTextChanged(s: Editable?) {
                    arrayListOfData[absoluteAdapterPosition].contact_email = (s ?: "").toString()
                }
            })
        }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_contact_detail, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
//        if (arrayListOfData.size == 1) {
//            holder.itemView.txtAddMore.visibility = View.VISIBLE
//        } else {
//            if (arrayListOfData != null)
//                if ((arrayListOfData.size - 1) == holder.absoluteAdapterPosition) {
//                    holder.itemView.txtAddMore.visibility = View.VISIBLE
//                } else {
//                    holder.itemView.txtAddMore.visibility = View.GONE
//                }
//        }
//        holder.itemView.txtAddMore.setOnClickListener { view ->
//            if ((arrayListOfData.size > holder.absoluteAdapterPosition)
//                && (holder.absoluteAdapterPosition != -1)
//            ) {
//                clickListener.onClickView(
//                    view,
//                    arrayListOfData[holder.absoluteAdapterPosition],
//                    holder.absoluteAdapterPosition
//                )
//            }
//        }

        if (!TextUtils.isEmpty(arrayListOfData[position].level)) {
            holder.itemView.txtSelectLevel.text = arrayListOfData[position].level
        }

        if (!TextUtils.isEmpty(arrayListOfData[position].designation)) {
            holder.itemView.txtSelectDesignation.text = arrayListOfData[position].designation
        }

        if (!TextUtils.isEmpty(arrayListOfData[position].contact_name)) {
            holder.itemView.etPersonName.setText(arrayListOfData[position].contact_name)
        }

        if (!TextUtils.isEmpty(arrayListOfData[position].wa_number)) {
            holder.itemView.etWaNumber.setText(arrayListOfData[position].wa_number)
        }

        if (!TextUtils.isEmpty(arrayListOfData[position].mobile_number)) {
            holder.itemView.etMobileNumber.setText(arrayListOfData[position].mobile_number)
        }

        if (!TextUtils.isEmpty(arrayListOfData[position].contact_email)) {
            holder.itemView.etEmail.setText(arrayListOfData[position].contact_email)
        }

        selectLevel = NumberSpinner(context as Activity, levelData, "Select Level")
        selectLevel?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                holder.itemView.txtSelectLevel.text = item
                arrayListOfData[holder.absoluteAdapterPosition].level = item
                notifyItemChanged(holder.absoluteAdapterPosition)
            }
        })

        selectDesignation = NumberSpinner(context, designationStringList, "Select Designation")
        selectDesignation?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                holder.itemView.txtSelectDesignation.text = item
                arrayListOfData[holder.absoluteAdapterPosition].designation = item
                notifyItemChanged(holder.absoluteAdapterPosition)
            }
        })

        holder.itemView.txtSelectLevel.setOnClickListener {
            selectLevel?.let {
                if (!it.isEnable) {
                    it.showSpinerDialog()
                }
            }
        }
        holder.itemView.txtSelectDesignation.setOnClickListener {
            selectDesignation?.let {
                if (!it.isEnable) {
                    it.showSpinerDialog()
                }
            }
        }
    }

    override fun getItemCount(): Int = arrayListOfData.size
    fun updateListData(
        levelList: ArrayList<String>,
        designationList: ArrayList<InfluencersDesignationData>
    ) {
        levelData.clear()
        designationData.clear()
        designationStringList.clear()
        levelData.addAll(levelList)
        designationData.addAll(designationList)
        designationData.forEach() {
            it.designation.let { it1 -> designationStringList.add(it1) }
        }
    }

    interface OnClickItem {
        fun onClickView(view: View, subMenu: InfluencersAddContactModel, position: Int)
    }

}