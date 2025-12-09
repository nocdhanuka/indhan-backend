package com.dhanuka.indhan.institutional.adapter

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.CircularListData
import kotlinx.android.synthetic.main.item_circulars_view.view.*

class CircularListAdapter(
    val context: Context,
    val circularList: List<CircularListData>
) : RecyclerView.Adapter<CircularListAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickItem

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): CircularListAdapter.ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_circulars_view, parent, false)
        return ViewHolder(inflater)
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    override fun onBindViewHolder(holder: CircularListAdapter.ViewHolder, position: Int) {
        bindData(holder, position)
    }

    private fun bindData(holder: ViewHolder, position: Int) {
        holder.itemView.txtCircularName.text = circularList[position].circular_name ?: "-"
        holder.itemView.tvCreatedDate.text = circularList[position].created_at ?: "-"
        holder.itemView.tvPublishDate.text = circularList[position].published_date ?: "-"
        holder.itemView.tvCircularFor.text =
            getTypeCircular(circularList[position].institutionalType)
        holder.itemView.tvLanguage.text = circularList[position].language_name ?: "-"
        holder.itemView.tvIsPublished.text =
            if (circularList[position].status == 1) "Published" else "Not Published"

        if (circularList[position].status == 1) {
            holder.itemView.txtEditCircular.visibility = View.GONE
            holder.itemView.txtViewCircular.visibility = View.VISIBLE
        } else {
            holder.itemView.txtViewCircular.visibility = View.VISIBLE
            holder.itemView.txtEditCircular.visibility = View.VISIBLE
        }

        holder.itemView.txtEditCircular.setOnClickListener {
            clickListener.onClickEdit(it, circularList[position])
        }
        holder.itemView.txtViewCircular.setOnClickListener {
            if (circularList[position].status == 1) {
                clickListener.onClickView(it, circularList[position], 0)
            } else {
                clickListener.onClickView(it, circularList[position], 1)
            }
        }

    }

    private fun getTypeCircular(institutionalType: String?): String {
        return when (institutionalType) {
            "1" -> {
                "Tea Estate"
            }
            "2" -> {
                "Sugar Mill"
            }
            "3" -> {
                "Influencer"
            }
            else -> {
                "-"
            }
        }
    }

    override fun getItemCount(): Int = circularList.size

    interface OnClickItem {
        fun onClickView(view: View, subMenu: CircularListData, status: Int)
        fun onClickEdit(view: View, subMenu: CircularListData)
    }

}