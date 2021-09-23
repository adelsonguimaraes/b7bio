<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Page;
use App\Models\Link;

class AdminController extends Controller
{

    public function __construct () {
        $this->middleware('auth', ['except'=>['login', 'loginAction', 'register', 'registerAction']]);
    }

    public function login(Request $request) {
        return view('admin/login', [
            'error'=> $request->session()->get('error')
        ]);
    }

    public function loginAction (Request $request) {
        $creds = $request->only('email', 'password');
        if (Auth::attempt($creds)) {
            return redirect('/admin');
        }else{
            $request->session()->flash('error', 'E-mail e/ou senha não conferem.');
            return redirect('/admin/login');
        }
    }

    public function register(Request $request) {
        return view('admin/register', [
            'error'=> $request->session()->get('error')
        ]);
    }

    public function registerAction (Request $request) {
        $creds = $request->only('email', 'password');

        $hasEmail = User::where('email', $creds['email'])->count();

        if ($hasEmail>0) {
            $request->session()->flash('error', 'Já existe um usuário com este e-mail!');
            return redirect('/admin/register');
        }

        $u = new User();
        $u->email = $creds['email'];
        $u->password = password_hash($creds['password'], PASSWORD_DEFAULT);
        $u->save();

        Auth::login($u);
        return redirect('/admin');
    }

    public function logout () {
        Auth::logout();
        return redirect('/admin');
    }

    public function index () {
        $user = Auth::user();
        $pages = Page::where('id_user', $user->id)->get();

        return view('admin/index', [
            'pages' => $pages
        ]);
    }

    public function pageLinks($slug) {
        $user = Auth::user();
        $page = Page::where('slug', $slug)
        ->where('id_user', $user->id)
        ->first();

        if (!$page) return redirect('/admin');

        $links = Link::where('id_page', $page->id)
        ->orderBy('order', 'ASC')
        ->get();

        return view('admin/page_links', [
            'menu' => 'links',
            'page' => $page,
            'links' => $links
        ]);
    }

    public function linkOrderUpdate ($linkid, $pos) {
        $user = Auth::user();

        // verificar se o link pertence a uma página do usuário logado
        // lógica para trocar o ORDER no banco de dados
        // verificar se subiu ou desceu
        // se subiu, aumenta todos pra +1, depois reorganiza
        // se desceu, dimui todos pra -1, depois reorganiza
        $link = Link::find($linkid);
        $pages = [];
        $query = Page::where('id_user', $user->id)->get();

        foreach($query as $q) {
            $pages[] = $q->id;
        }
        
        if (in_array($link->id_page, $pages)) {
            if ($link->order>$pos) {
                // subiu, jogando os proximos pra baixo
                $afterLinks = Link::where('id_page', $link->id_page)
                ->where('order', '>=', $pos)
                ->get();
                foreach($afterLinks as $afterLink) {
                    $afterLink->order++;
                    $afterLink->save();
                }
            }elseif($link->order<$pos) {
                // desceu, jogando os anterios pra cima
                $beforeLinks = Link::where('id_page', $link->id_page)
                ->where('order', '<=', $pos)
                ->get();
                foreach($beforeLinks as $beforeLink) {
                    $beforeLink->order--;
                    $beforeLink->save();
                }
            }

            // posicionando o item
            $link->order = $pos;
            $link->save();

            // reorganizar links
            $allLinks = Link::where('id_page', $link->id_page)
            ->orderBy('order', 'ASC')
            ->get();
            foreach($allLinks as $linkKey => $linkItem) {
                $linkItem->order = $linkKey;
                $linkItem->save();
            }
        }

        return [];
    }

    public function newLink($slug) {
        $user = Auth::user();
        $page = Page::where('id_user', $user->id)
        ->where('slug', $slug)
        ->first();

        if ($page) {
            return view('admin/page_editlink', [
                'menu' => 'links',
                'page' => $page
            ]);
        }else{
            return redirect('/admin');
        }
    }

    public function newLinkAction ($slug, Request $request) {
        $user = Auth::user();
        $page = Page::where('id_user', $user->id)
        ->where('slug', $slug)
        ->first();

        if (!$page) {
            return redirect('/admin');
        }

        $fields = $request->validate([
            'status' => ['required', 'boolean'],
            'title' => ['required', 'min:2'],
            'href' => ['required', 'url'],
            'op_bg_color' => ['required', 'regex:/^[#][0-9A-F]{3,6}$/i'],
            'op_text_color' => ['required', 'regex:/^[#][0-9A-F]{3,6}$/i'],
            'op_border_type' => ['required', Rule::in(['square', 'rounded'])]
        ]);

         $totallinks = Link::where('id_page', $page->id)->count();

         $l = new Link();
         $l->id_page = $page->id;
         $l->status = $fields['status'];
         $l->order = $totallinks;
         $l->title = $fields['title'];
         $l->href = $fields['href'];
         $l->op_bg_color = $fields['op_bg_color'];
         $l->op_text_color = $fields['op_text_color'];
         $l->op_border_type = $fields['op_border_type'];
         $l->save();

         return redirect('/admin/'.$page->slug.'/links');
    }

    public function editLink($slug, $linkid) {
        $user = Auth::user();
        $page = Page::where('id_user', $user->id)
        ->where('slug', $slug)
        ->first();

        if ($page) {
            $link = Link::where('id_page', $page->id)
            ->where('id', $linkid)
            ->first();

            if ($link) {
                return view('admin/page_editlink', [
                    'menu' => 'links',
                    'page' => $page,
                    'link' => $link
                ]);
            }
        }

        return redirect('/admin');
    }

    public function editLinkAction($slug, $linkid, Request $request) {
        $user = Auth::user();
        $page = Page::where('id_user', $user->id)
        ->where('slug', $slug)
        ->first();

        if ($page) {
            $link = Link::where('id_page', $page->id)
            ->where('id', $linkid)
            ->first();

            if ($link) {
                $fields = $request->validate([
                    'status' => ['required', 'boolean'],
                    'title' => ['required', 'min:2'],
                    'href' => ['required', 'url'],
                    'op_bg_color' => ['required', 'regex:/^[#][0-9A-F]{3,6}$/i'],
                    'op_text_color' => ['required', 'regex:/^[#][0-9A-F]{3,6}$/i'],
                    'op_border_type' => ['required', Rule::in(['square', 'rounded'])]
                ]);

                $link->status = $fields['status'];
                $link->title = $fields['title'];
                $link->href = $fields['href'];
                $link->op_bg_color = $fields['op_bg_color'];
                $link->op_text_color = $fields['op_text_color'];
                $link->op_border_type = $fields['op_border_type'];
                $link->save();

                return redirect('/admin/'.$page->slug.'/links');
            }
        }

        return redirect('/admin');
    }

    public function delLink($slug, $linkid) {
        $user = Auth::user();
        $page = Page::where('id_user', $user->id)
        ->where('slug', $slug)
        ->first();

        if ($page) {
            $link = Link::where('id_page', $page->id)
            ->where('id', $linkid)
            ->first();

            if ($link) {
                $link->delete();

                // reorganizar links
                $allLinks = Link::where('id_page', $page->id)
                ->orderBy('order', 'ASC')
                ->get();
                foreach($allLinks as $linkKey => $linkItem) {
                    $linkItem->order = $linkKey;
                    $linkItem->save();
                }

                return redirect('/admin/'.$page->slug.'/links');
            }
        }

        return redirect('/admin');
    }

    public function pageDesign($slug) {
        return view('admin/page_design', [
            'menu' => 'design'
        ]);
    }

    public function pageStats($slug) {
        return view('admin/page_stats', [
            'menu' => 'stats'
        ]);
    }
}
