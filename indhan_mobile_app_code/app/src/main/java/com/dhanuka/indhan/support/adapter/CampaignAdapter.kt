package com.dhanuka.indhan.support.adapter

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.RelativeLayout
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.model.CampaignModel
import kotlinx.android.synthetic.main.layout_menu_item.view.tvMeCategoryName
import kotlinx.android.synthetic.main.layout_submenu_item.view.*

class CampaignAdapter(val context: Context, val campaignList: List<CampaignModel>) :
    RecyclerView.Adapter<CampaignAdapter.ViewHolder>() {

    private lateinit var clickListener: CampaignClickListener

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((campaignList != null) && (campaignList.size > adapterPosition) && (adapterPosition != -1)) {
                    clickListener.onClick(view, campaignList[adapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: CampaignClickListener) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_submenu_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val catName = holder.itemView.tvMeCategoryName
        holder.itemView.mandatoryIcon.visibility = View.GONE
        holder.itemView.filledIcon.visibility = View.GONE
        val layoutParams = catName.layoutParams as RelativeLayout.LayoutParams
//        layoutParams.addRule(RelativeLayout.CENTER_VERTICAL, RelativeLayout.TRUE)
        catName.layoutParams = layoutParams
        catName.textSize = 16f
        if (campaignList[position].campaignName?.length!! > 40) {
            val cName: String =
                campaignList[position].campaignName!!.subSequence(0, 39).toString() + "..."
            catName.text = cName
        } else {
            catName.text = campaignList[position].campaignName
        }
        if (campaignList[position].camp_response == 'N' && campaignList[position].camp_mandatory == 1) {
            holder.itemView.mandatoryIcon.visibility = View.VISIBLE
        } else if (campaignList[position].camp_response == 'F' && campaignList[position].camp_mandatory == 1) {
            holder.itemView.filledIcon.visibility = View.VISIBLE
            holder.itemView.mandatoryIcon.visibility = View.VISIBLE
        } else if (campaignList[position].camp_mandatory == 1) {
            holder.itemView.mandatoryIcon.visibility = View.VISIBLE
        } else if (campaignList[position].camp_response == 'F') {
            holder.itemView.filledIcon.visibility = View.VISIBLE
        }
    }

    override fun getItemCount(): Int {
        return campaignList.size
    }

    interface CampaignClickListener {
        fun onClick(view: View, subMenu: CampaignModel)
    }

}