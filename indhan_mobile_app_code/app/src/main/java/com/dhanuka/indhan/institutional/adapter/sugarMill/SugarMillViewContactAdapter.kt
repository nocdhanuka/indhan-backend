package com.dhanuka.indhan.institutional.adapter.sugarMill

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.sugarMill.ContactListData
import kotlinx.android.synthetic.main.item_view_contact_detail.view.*

class SugarMillViewContactAdapter(
    private val context: Context,
    private val arrayListOfData: ArrayList<ContactListData>
) : RecyclerView.Adapter<SugarMillViewContactAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickItem

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((arrayListOfData != null) && (arrayListOfData.size > absoluteAdapterPosition) && (absoluteAdapterPosition != -1)) {
                    clickListener.onClickView(view, arrayListOfData[absoluteAdapterPosition])
                }
            }
        }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_view_contact_detail, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.itemView.tvLabelContactNo.text = "Contact ${position + 1}"
        holder.itemView.tvLevelName.text = arrayListOfData[position].level.toString()
        holder.itemView.tvDesignation.text = arrayListOfData[position].designation
        holder.itemView.tvPersonName.text = arrayListOfData[position].name
        holder.itemView.tvWaNumber.text = arrayListOfData[position].wa_number
        holder.itemView.tvMobileNumber.text = arrayListOfData[position].mobile_number
        holder.itemView.tvEmail.text = arrayListOfData[position].email
    }

    override fun getItemCount(): Int = arrayListOfData.size

    interface OnClickItem {
        fun onClickView(view: View, subMenu: ContactListData)
    }

}