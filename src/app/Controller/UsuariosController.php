<?php
App::uses('AppController', 'Controller');
/**
 * Usuarios Controller
 *
 * @property Usuario $Usuario
 */
class UsuariosController extends AppController {
 
    /**
     * 
     */    
        public function login () {
            if ( !$this->Session->read('user') ) {
                if ( $this->request->is('post') ) {
                    
                    $result = $this->Usuario->findByEmail($this->request->data['Usuario']['email']);
                    
                    if ( $result )  {
                        if ( md5($this->request->data['Usuario']['senha']) == $result['Usuario']['senha'] ) {
                            $dadosUsuario = array('id' => $result['Usuario']['id'], 'nome' => $result['Usuario']['nome'], 'email' => $result['Usuario']['email']);
                            if ( $this->inicia_sessao($dadosUsuario) ) {
                                $this->redirect('/despesas/relatorio/');
                            } else {
                                $this->set('erroLogin', 1);
                            }
                        } else {
                            $this->set('erroLogin', 1);
                        } 
                    } else {
                        $this->set('erroLogin', 1);
                    }
                }
            } else {
                $this->redirect('/despesas/relatorio/');
            }
        }
        
       
  /**
   * inicia_sessao method
   * 
   * @param type $dadosUsuario
   * @return boolean
   */      
        private function inicia_sessao ( $dadosUsuario ) {
            if ( $this->Session->write('user', $dadosUsuario) ) {
                return true;
            }
        }
        
        
  /**
   * 
   */      
        public function logout () {
            $this->Session->delete('user');
            $this->Session->setFlash('<p>Sessão finalizada com sucesso.</p>', 'default', array('class' => 'notification msgsuccess'));
            $this->redirect('/');
        }
        
   
 /**
  * cadastro method
  * 
  */      
        public function cadastro () {
            if ( !$this->Session->read('user') ) {
                if ( $this->request->is('post') ) {
                    $this->request->data['Usuario']['senha'] = md5($this->request->data['Usuario']['senha']);
                    
                    $this->Usuario->create();
                    
                    $tipoPermitidos = array('image/jpeg','image/png');
                    if ( @$this->request->data['Usuario']['avatar'] && @$this->request->data['Usuario']['avatar']['type'] == 'image/png' || @$this->request->data['Usuario']['avatar']['type'] == 'image/jpeg' ) {
                        if (move_uploaded_file($this->request->data['Usuario']['avatar']['tmp_name'], 'img/users/'.$this->request->data['Usuario']['nome'].'.jpg') ) {
                            $this->request->data['Usuario']['avatar'] = $this->request->data['Usuario']['nome'].'.jpg';
                        }
                    } else if ( @$this->request->data['Usuario']['avatar']['type'] != 'image/png' && @$this->request->data['Usuario']['avatar']['type'] != 'image/jpeg' ){
                        $this->Session->setFlash('<p>Não foi possível realizar o upload da imagem. Tipo de arquivo não suportado. Tipos permitidos: jpeg e png. Enviado: '.@$this->request->data['Usuario']['avatar']['type'].'</p>', 
                                                            'default', array('class' => 'notification msgerror'));
                        $this->Usuario->data['Usuario']['avatar'] = '';
                    } else {
                        $this->Usuario->data['Usuario']['avatar'] = '';
                    }
                   
                    
                    if ( $this->Usuario->save($this->request->data) ) {
                        $this->request->data['Usuario']['id'] = $this->Usuario->id;
                        if ( $this->inicia_sessao($this->request->data['Usuario']) ) {
                            $this->Session->setFlash('<p>Cadastro realizado com sucesso!</p>', 'default', array('class' => 'notification msgsuccess'));
                            $this->redirect('/');
                        }
                    } else {
                        $this->Session->setFlash('<p>Não foi possível realizar seu cadastro, por favor tente novamente!</p>', 'default', array('class' => 'notification msgerror'));
                    }
                }
            } else {
                $this->redirect('/');
            }
        }
       
}